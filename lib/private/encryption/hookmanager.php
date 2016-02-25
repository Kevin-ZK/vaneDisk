<?php

namespace OC\Encryption;

use OC\Files\Filesystem;
use OC\Files\View;

class HookManager {
	/**
	 * @var Update
	 */
	private static $updater;

	public static function postShared($params) {
		self::getUpdate()->postShared($params);
	}
	public static function postUnshared($params) {
		self::getUpdate()->postUnshared($params);
	}

	public static function postRename($params) {
		self::getUpdate()->postRename($params);
	}

	public static function postRestore($params) {
		self::getUpdate()->postRestore($params);
	}

	/**
	 * @return Update
	 */
	private static function getUpdate() {
		if (is_null(self::$updater)) {
			$user = \OC::$server->getUserSession()->getUser();
			$uid = '';
			if ($user) {
				$uid = $user->getUID();
			}
			self::$updater = new Update(
				new View(),
				new Util(
					new View(),
					\OC::$server->getUserManager(),
					\OC::$server->getGroupManager(),
					\OC::$server->getConfig()),
				Filesystem::getMountManager(),
				\OC::$server->getEncryptionManager(),
				\OC::$server->getEncryptionFilesHelper(),
				$uid
			);
		}

		return self::$updater;
	}
}
