<?php

namespace OCA\Activity;

use OCP\Activity\IConsumer;
use OCP\Activity\IManager;
use OCP\AppFramework\IAppContainer;

class Consumer implements IConsumer {
	/**
	 * Registers the consumer to the Activity Manager
	 *
	 * @param IManager $am
	 * @param IAppContainer $container
	 */
	public static function register(IManager $am, IAppContainer $container) {
		$am->registerConsumer(function() use ($am, $container) {
			return $container->query('Consumer');
		});
	}

	/** @var UserSettings */
	protected $userSettings;

	/** @var string */
	protected $user;

	/**
	 * Constructor
	 *
	 * @param UserSettings $userSettings
	 * @param string $user
	 */
	public function __construct(UserSettings $userSettings, $user) {
		$this->userSettings = $userSettings;
		$this->user = $user;
	}

	/**
	 * Send an event into the activity stream of a user
	 *
	 * @param string $app The app where this event is associated with
	 * @param string $subject A short description of the event
	 * @param array  $subjectParams Array with parameters that are filled in the subject
	 * @param string $message A longer description of the event
	 * @param array  $messageParams Array with parameters that are filled in the message
	 * @param string $file The file including path where this event is associated with. (optional)
	 * @param string $link A link where this event is associated with (optional)
	 * @param string $affectedUser If empty the current user will be used
	 * @param string $type Type of the notification
	 * @param int    $priority Priority of the notification
	 * @return null
	 */
	public function receive($app, $subject, $subjectParams, $message, $messageParams, $file, $link, $affectedUser, $type, $priority) {
		$selfAction = $affectedUser === $this->user;
		$streamSetting = $this->userSettings->getUserSetting($affectedUser, 'stream', $type);
		$emailSetting = $this->userSettings->getUserSetting($affectedUser, 'email', $type);
		$emailSetting = ($emailSetting) ? $this->userSettings->getUserSetting($affectedUser, 'setting', 'batchtime') : false;

		// Add activity to stream
		if ($streamSetting && (!$selfAction || $this->userSettings->getUserSetting($affectedUser, 'setting', 'self'))) {
			Data::send($app, $subject, $subjectParams, $message, $messageParams, $file, $link, $affectedUser, $type, $priority);
		}

		// Add activity to mail queue
		if ($emailSetting && (!$selfAction || $this->userSettings->getUserSetting($affectedUser, 'setting', 'selfemail'))) {
			$latestSend = time() + $emailSetting;
			Data::storeMail($app, $subject, $subjectParams, $affectedUser, $type, $latestSend);
		}
	}
}
