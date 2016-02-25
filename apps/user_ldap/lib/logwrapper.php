<?php

namespace OCA\user_ldap\lib;

/**
 * @brief wraps around static ownCloud core methods
 */
class LogWrapper {
	protected $app = 'user_ldap';

	/**
	 * @brief states whether the filesystem was loaded
	 * @return bool
	 */
	public function log($msg, $level) {
		\OCP\Util::writeLog($this->app, $msg, $level);
	}
}
