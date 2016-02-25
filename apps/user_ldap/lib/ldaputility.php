<?php

namespace OCA\user_ldap\lib;

abstract class LDAPUtility {
	protected $ldap;

	/**
	 * constructor, make sure the subclasses call this one!
	 * @param ILDAPWrapper $ldapWrapper an instance of an ILDAPWrapper
	 */
	public function __construct(ILDAPWrapper $ldapWrapper) {
		$this->ldap = $ldapWrapper;
	}
}
