<?php

namespace OCA\user_ldap\lib;

use OCA\user_ldap\lib\Access;

abstract class BackendUtility {
	protected $access;

	/**
	 * constructor, make sure the subclasses call this one!
	 * @param Access $access an instance of Access for LDAP interaction
	 */
	public function __construct(Access $access) {
		$this->access = $access;
	}
}
