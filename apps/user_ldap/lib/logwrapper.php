<?php
/**
 *
 * @copyright Copyright (c) 2015, ownCloud, Inc.
 * @license AGPL-3.0
 *
 * This code is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License, version 3,
 * as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License, version 3,
 * along with this program.  If not, see <http://www.gnu.org/licenses/>
 *
 */

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