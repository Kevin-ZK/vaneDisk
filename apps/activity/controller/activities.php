<?php

namespace OCA\Activity\Controller;

use OC\Files\View;
use OCA\Activity\Data;
use OCA\Activity\Display;
use OCA\Activity\GroupHelper;
use OCA\Activity\Navigation;
use OCA\Activity\UserSettings;
use OCP\AppFramework\Controller;
use OCP\AppFramework\Http\TemplateResponse;
use OCP\IRequest;

class Activities extends Controller {
	const DEFAULT_PAGE_SIZE = 30;

	/** @var \OCA\Activity\Data */
	protected $data;

	/** @var \OCA\Activity\Display */
	protected $display;

	/** @var \OCA\Activity\GroupHelper */
	protected $helper;

	/** @var \OCA\Activity\Navigation */
	protected $navigation;

	/** @var \OCA\Activity\UserSettings */
	protected $settings;

	/** @var string */
	protected $user;

	/**
	 * constructor of the controller
	 *
	 * @param string $appName
	 * @param IRequest $request
	 * @param Data $data
	 * @param Display $display
	 * @param GroupHelper $helper
	 * @param Navigation $navigation
	 * @param UserSettings $settings
	 * @param string $user
	 */
	public function __construct($appName, IRequest $request, Data $data, Display $display, GroupHelper $helper, Navigation $navigation, UserSettings $settings, $user) {
		parent::__construct($appName, $request);
		$this->data = $data;
		$this->display = $display;
		$this->helper = $helper;
		$this->navigation = $navigation;
		$this->settings = $settings;
		$this->user = $user;
	}

	/**
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 *
	 * @param string $filter
	 * @return TemplateResponse
	 */
	public function showList($filter = 'all') {
		$filter = $this->data->validateFilter($filter);

		return new TemplateResponse('activity', 'stream.body', [
			'appNavigation'	=> $this->navigation->getTemplate($filter),
			'filter'		=> $filter,
		]);
	}

	/**
	 * @NoAdminRequired
	 *
	 * @param int $page
	 * @param string $filter
	 * @return TemplateResponse
	 */
	public function fetch($page, $filter = 'all') {
		$pageOffset = $page - 1;
		$filter = $this->data->validateFilter($filter);

		return new TemplateResponse('activity', 'stream.list', [
			'activity'		=> $this->data->read($this->helper, $this->settings, $pageOffset * self::DEFAULT_PAGE_SIZE, self::DEFAULT_PAGE_SIZE, $filter),
			'displayHelper'	=> $this->display,
		], '');
	}
}
