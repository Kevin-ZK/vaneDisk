<?php

namespace OCA\user_ldap\lib;

/**
 * @brief wraps around static ownCloud core methods
 */
class FilesystemHelper {

	/**
	 * @brief states whether the filesystem was loaded
	 * @return bool
	 */
	public function isLoaded() {
		return \OC\Files\Filesystem::$loaded;
	}

	/**
	 * @brief initializes the filesystem for the given user
	 * @param string the ownCloud username of the user
	 */
	public function setup($uid) {
		\OC_Util::setupFS($uid);
	}
}
