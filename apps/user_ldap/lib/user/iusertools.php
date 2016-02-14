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

namespace OCA\user_ldap\lib\user;

/**
 * IUserTools
 *
 * defines methods that are required by User class for LDAP interaction
 */
interface IUserTools {
	public function getConnection();

	public function readAttribute($dn, $attr, $filter = 'objectClass=*');

	public function stringResemblesDN($string);

	public function dn2username($dn, $ldapname = null);

	public function username2dn($name);
}
