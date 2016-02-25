<?php

namespace OCA\user_ldap\tests\integration;

/**
 * Class FakeManager
 *
 * this is a mock of \OCA\user_ldap\lib\user\Manager which is a dependency of
 * Access, that pulls plenty more things in. Because it is not needed in the
 * scope of these tests, we replace it with a mock.
 */
class FakeManager extends \OCA\user_ldap\lib\user\Manager {
	public function __construct() {}
}
