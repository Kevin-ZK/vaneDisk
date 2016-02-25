<?php

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
