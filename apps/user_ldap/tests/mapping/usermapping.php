<?php

namespace OCA\user_ldap\tests\mapping;

use OCA\User_LDAP\Mapping\UserMapping;

class Test_UserMapping extends AbstractMappingTest {
	public function getMapper(\OCP\IDBConnection $dbMock) {
		return new UserMapping($dbMock);
	}
}
