<?php

namespace OC\Core;

use OC\AppFramework\Utility\SimpleContainer;
use \OCP\AppFramework\App;
use OC\Core\LostPassword\Controller\LostController;
use OC\Core\User\UserController;
use OC\Core\Avatar\AvatarController;
use \OCP\Util;

/**
 * Class Application
 *
 * @package OC\Core
 */
class Application extends App {

	/**
	 * @param array $urlParams
	 */
	public function __construct(array $urlParams=array()){
		parent::__construct('core', $urlParams);

		$container = $this->getContainer();

		/**
		 * Controllers
		 */
		$container->registerService('LostController', function(SimpleContainer $c) {
			return new LostController(
				$c->query('AppName'),
				$c->query('Request'),
				$c->query('URLGenerator'),
				$c->query('UserManager'),
				$c->query('Defaults'),
				$c->query('L10N'),
				$c->query('Config'),
				$c->query('SecureRandom'),
				$c->query('DefaultEmailAddress'),
				$c->query('IsEncryptionEnabled'),
				$c->query('Mailer')
			);
		});
		$container->registerService('UserController', function(SimpleContainer $c) {
			return new UserController(
				$c->query('AppName'),
				$c->query('Request'),
				$c->query('UserManager'),
				$c->query('Defaults')
			);
		});
		$container->registerService('AvatarController', function(SimpleContainer $c) {
			return new AvatarController(
				$c->query('AppName'),
				$c->query('Request'),
				$c->query('AvatarManager'),
				$c->query('Cache'),
				$c->query('L10N'),
				$c->query('UserManager'),
				$c->query('UserSession')
			);
		});

		/**
		 * Core class wrappers
		 */
		$container->registerService('IsEncryptionEnabled', function(SimpleContainer $c) {
			return $c->query('ServerContainer')->getEncryptionManager()->isEnabled();
		});
		$container->registerService('URLGenerator', function(SimpleContainer $c) {
			return $c->query('ServerContainer')->getURLGenerator();
		});
		$container->registerService('UserManager', function(SimpleContainer $c) {
			return $c->query('ServerContainer')->getUserManager();
		});
		$container->registerService('Config', function(SimpleContainer $c) {
			return $c->query('ServerContainer')->getConfig();
		});
		$container->registerService('L10N', function(SimpleContainer $c) {
			return $c->query('ServerContainer')->getL10N('core');
		});
		$container->registerService('SecureRandom', function(SimpleContainer $c) {
			return $c->query('ServerContainer')->getSecureRandom();
		});
		$container->registerService('AvatarManager', function(SimpleContainer $c) {
			return $c->query('ServerContainer')->getAvatarManager();
		});
		$container->registerService('UserSession', function(SimpleContainer $c) {
			return $c->query('ServerContainer')->getUserSession();
		});
		$container->registerService('Cache', function(SimpleContainer $c) {
			return $c->query('ServerContainer')->getCache();
		});


		$container->registerService('Defaults', function() {
			return new \OC_Defaults;
		});
		$container->registerService('Mailer', function(SimpleContainer $c) {
			return $c->query('ServerContainer')->getMailer();
		});
		$container->registerService('DefaultEmailAddress', function() {
			return Util::getDefaultEmailAddress('lostpassword-noreply');
		});
	}

}
