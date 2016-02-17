<?php

namespace OCA\Activity\Controller;

use OCA\Activity\Data;
use OCA\Activity\UserSettings;
use OCP\AppFramework\Controller;
use OCP\AppFramework\Http\DataResponse;
use OCP\AppFramework\Http\TemplateResponse;
use OCP\IConfig;
use OCP\IL10N;
use OCP\IRequest;
use OCP\IURLGenerator;
use OCP\Security\ISecureRandom;

class Settings extends Controller {
	/** @var \OCP\IConfig */
	protected $config;

	/** @var \OCP\Security\ISecureRandom */
	protected $random;

	/** @var \OCP\IURLGenerator */
	protected $urlGenerator;

	/** @var \OCA\Activity\Data */
	protected $data;

	/** @var \OCA\Activity\UserSettings */
	protected $userSettings;

	/** @var \OCP\IL10N */
	protected $l10n;

	/** @var string */
	protected $user;

	/**
	 * constructor of the controller
	 *
	 * @param string $appName
	 * @param IRequest $request
	 * @param IConfig $config
	 * @param ISecureRandom $random
	 * @param IURLGenerator $urlGenerator
	 * @param Data $data
	 * @param UserSettings $userSettings
	 * @param IL10N $l10n
	 * @param string $user
	 */
	public function __construct($appName, IRequest $request, IConfig $config, ISecureRandom $random, IURLGenerator $urlGenerator, Data $data, UserSettings $userSettings, IL10N $l10n, $user) {
		parent::__construct($appName, $request);
		$this->config = $config;
		$this->random = $random;
		$this->urlGenerator = $urlGenerator;
		$this->data = $data;
		$this->userSettings = $userSettings;
		$this->l10n = $l10n;
		$this->user = $user;
	}

	/**
	 * @NoAdminRequired
	 *
	 * @param int $notify_setting_batchtime
	 * @param bool $notify_setting_self
	 * @param bool $notify_setting_selfemail
	 * @return DataResponse
	 */
	public function personal(
			$notify_setting_batchtime = UserSettings::EMAIL_SEND_HOURLY,
			$notify_setting_self = false,
			$notify_setting_selfemail = false) {
		$types = $this->data->getNotificationTypes($this->l10n);

		foreach ($types as $type => $desc) {
			$this->config->setUserValue(
				$this->user, 'activity',
				'notify_email_' . $type,
				$this->request->getParam($type . '_email', false)
			);

			$this->config->setUserValue(
				$this->user, 'activity',
				'notify_stream_' . $type,
				$this->request->getParam($type . '_stream', false)
			);
		}

		$email_batch_time = 3600;
		if ($notify_setting_batchtime === UserSettings::EMAIL_SEND_DAILY) {
			$email_batch_time = 3600 * 24;
		} else if ($notify_setting_batchtime === UserSettings::EMAIL_SEND_WEEKLY) {
			$email_batch_time = 3600 * 24 * 7;
		}

		$this->config->setUserValue(
			$this->user, 'activity',
			'notify_setting_batchtime',
			$email_batch_time
		);
		$this->config->setUserValue(
			$this->user, 'activity',
			'notify_setting_self',
			$notify_setting_self
		);
		$this->config->setUserValue(
			$this->user, 'activity',
			'notify_setting_selfemail',
			$notify_setting_selfemail
		);

		return new DataResponse(array(
			'data'		=> array(
				'message'	=> (string) $this->l10n->t('Your settings have been updated.'),
			),
		));
	}

	/**
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 *
	 * @return TemplateResponse
	 */
	public function displayPanel() {
		$types = $this->data->getNotificationTypes($this->l10n);

		$activities = array();
		foreach ($types as $type => $desc) {
			$activities[$type] = array(
				'desc'		=> $desc,
				'email'		=> $this->userSettings->getUserSetting($this->user, 'email', $type),
				'stream'	=> $this->userSettings->getUserSetting($this->user, 'stream', $type),
			);
		}

		$settingBatchTime = UserSettings::EMAIL_SEND_HOURLY;
		$currentSetting = (int) $this->userSettings->getUserSetting($this->user, 'setting', 'batchtime');
		if ($currentSetting === 3600 * 24 * 7) {
			$settingBatchTime = UserSettings::EMAIL_SEND_WEEKLY;
		} else if ($currentSetting === 3600 * 24) {
			$settingBatchTime = UserSettings::EMAIL_SEND_DAILY;
		}

		return new TemplateResponse('activity', 'personal', [
			'activities'		=> $activities,
			'activity_email'	=> $this->config->getUserValue($this->user, 'settings', 'email', ''),

			'setting_batchtime'	=> $settingBatchTime,

			'notify_self'		=> $this->userSettings->getUserSetting($this->user, 'setting', 'self'),
			'notify_selfemail'	=> $this->userSettings->getUserSetting($this->user, 'setting', 'selfemail'),
		], '');
	}

	/**
	 * @NoAdminRequired
	 *
	 * @param string $enable	'true' if the feed is enabled
	 * @return DataResponse
	 */
	public function feed($enable) {
		$token = $tokenUrl = '';

		if ($enable === 'true') {
			$conflicts = true;

			// Check for collisions
			while (!empty($conflicts)) {
				$token = $this->random->generate(30);
				$conflicts = $this->config->getUsersForUserValue('activity', 'rsstoken', $token);
			}

			$tokenUrl = $this->urlGenerator->linkToRouteAbsolute('activity.Feed.show', ['token' => $token]);
		}

		$this->config->setUserValue($this->user, 'activity', 'rsstoken', $token);

		return new DataResponse(array(
			'data'		=> array(
				'message'	=> (string) $this->l10n->t('Your settings have been updated.'),
				'rsslink'	=> $tokenUrl,
			),
		));
	}
}
