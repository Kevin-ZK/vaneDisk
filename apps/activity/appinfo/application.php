<?php

namespace OCA\Activity\AppInfo;

use OC\Files\View;
use OCA\Activity\Consumer;
use OCA\Activity\Controller\Activities;
use OCA\Activity\Controller\Feed;
use OCA\Activity\Controller\Settings;
use OCA\Activity\Data;
use OCA\Activity\DataHelper;
use OCA\Activity\Display;
use OCA\Activity\GroupHelper;
use OCA\Activity\FilesHooks;
use OCA\Activity\MailQueueHandler;
use OCA\Activity\MockUtilSendMail;
use OCA\Activity\Navigation;
use OCA\Activity\ParameterHelper;
use OCA\Activity\UserSettings;
use OCP\AppFramework\App;
use OCP\IContainer;

class Application extends App {
	public function __construct (array $urlParams = array()) {
		parent::__construct('activity', $urlParams);
		$container = $this->getContainer();

		/**
		 * Activity Services
		 */
		$container->registerService('ActivityData', function(IContainer $c) {
			return new Data(
				$c->query('ServerContainer')->query('ActivityManager')
			);
		});

		$container->registerService('ActivityL10N', function(IContainer $c) {
			return $c->query('ServerContainer')->getL10N('activity');
		});


		$container->registerService('Consumer', function(IContainer $c) {
			return new Consumer(
				$c->query('UserSettings'),
				$c->query('CurrentUID')
			);
		});

		$container->registerService('DataHelper', function(IContainer $c) {
			/** @var \OC\Server $server */
			$server = $c->query('ServerContainer');
			return new DataHelper(
				$server->getActivityManager(),
				new ParameterHelper (
					$server->getActivityManager(),
					$server->getUserManager(),
					new View(''),
					$server->getConfig(),
					$c->query('ActivityL10N'),
					$c->query('CurrentUID')
				),
				$c->query('ActivityL10N')
			);
		});

		$container->registerService('DisplayHelper', function(IContainer $c) {
			/** @var \OC\Server $server */
			$server = $c->query('ServerContainer');

			return new Display(
				$server->query('DateTimeFormatter'),
				$server->getPreviewManager(),
				$server->getURLGenerator(),
				new View('')
			);
		});

		$container->registerService('GroupHelper', function(IContainer $c) {
			return new GroupHelper(
				$c->query('ServerContainer')->getActivityManager(),
				$c->query('DataHelper'),
				true
			);
		});

		$container->registerService('GroupHelperSingleEntries', function(IContainer $c) {
			return new GroupHelper(
				$c->query('ServerContainer')->getActivityManager(),
				$c->query('DataHelper'),
				false
			);
		});

		$container->registerService('Hooks', function(IContainer $c) {
			return new FilesHooks(
				$c->query('ActivityData'),
				$c->query('UserSettings'),
				$c->query('CurrentUID')
			);
		});

		$container->registerService('MailQueueHandler', function(IContainer $c) {
			/** @var \OC\Server $server */
			$server = $c->query('ServerContainer');

			return new MailQueueHandler(
				$server->getDateTimeFormatter(),
				$server->getDatabaseConnection(),
				$c->query('DataHelper'),
				$server->getMailer(),
				$server->getURLGenerator(),
				$server->getUserManager()
			);
		});

		$container->registerService('Navigation', function(IContainer $c) {
			/** @var \OC\Server $server */
			$server = $c->query('ServerContainer');
			$rssToken = ($c->query('CurrentUID') !== '') ? $server->getConfig()->getUserValue($c->query('CurrentUID'), 'activity', 'rsstoken') : '';

			return new Navigation(
				$c->query('ActivityL10N'),
				$server->getActivityManager(),
				$server->getURLGenerator(),
				$c->query('UserSettings'),
				$c->query('CurrentUID'),
				$rssToken
			);
		});

		$container->registerService('UserSettings', function(IContainer $c) {
			/** @var \OC\Server $server */
			$server = $c->query('ServerContainer');
			return new UserSettings(
				$server->getActivityManager(),
				$server->getConfig(),
				$c->query('ActivityData')
			);
		});

		/**
		 * Core Services
		 */
		$container->registerService('URLGenerator', function(IContainer $c) {
			/** @var \OC\Server $server */
			$server = $c->query('ServerContainer');
			return $server->getURLGenerator();
		});

		$container->registerService('CurrentUID', function(IContainer $c) {
			/** @var \OC\Server $server */
			$server = $c->query('ServerContainer');

			$user = $server->getUserSession()->getUser();
			return ($user) ? $user->getUID() : '';
		});

		/**
		 * Controller
		 */
		$container->registerService('SettingsController', function(IContainer $c) {
			/** @var \OC\Server $server */
			$server = $c->query('ServerContainer');

			return new Settings(
				$c->query('AppName'),
				$c->query('Request'),
				$server->getConfig(),
				$server->getSecureRandom()->getMediumStrengthGenerator(),
				$c->query('URLGenerator'),
				$c->query('ActivityData'),
				$c->query('UserSettings'),
				$c->query('ActivityL10N'),
				$c->query('CurrentUID')
			);
		});

		$container->registerService('ActivitiesController', function(IContainer $c) {
			return new Activities(
				$c->query('AppName'),
				$c->query('Request'),
				$c->query('ActivityData'),
				$c->query('DisplayHelper'),
				$c->query('GroupHelper'),
				$c->query('Navigation'),
				$c->query('UserSettings'),
				$c->query('CurrentUID')
			);
		});

		$container->registerService('FeedController', function(IContainer $c) {
			/** @var \OC\Server $server */
			$server = $c->query('ServerContainer');

			return new Feed(
				$c->query('AppName'),
				$c->query('Request'),
				$c->query('ActivityData'),
				$c->query('GroupHelperSingleEntries'),
				$c->query('UserSettings'),
				$c->query('URLGenerator'),
				$server->getActivityManager(),
				$server->getConfig(),
				$c->query('CurrentUID')
			);
		});
	}
}
