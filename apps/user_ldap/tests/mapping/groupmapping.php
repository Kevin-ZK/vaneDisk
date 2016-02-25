<?php

namespace OCA\user_ldap\tests\mapping;

use OCA\User_LDAP\Mapping\GroupMapping;

class Test_GroupMapping extends AbstractMappingTest {
	public function getMapper(\OCP\IDBConnection $dbMock) {
		return new GroupMapping($dbMock);
	}
}
