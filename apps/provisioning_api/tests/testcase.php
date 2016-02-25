<?php

namespace OCA\Provisioning_API\Tests;

abstract class TestCase extends \Test\TestCase {
	protected $users = array();

	protected function setUp() {
		parent::setUp();
		\OC_Group::createGroup('admin');
	}

	/**
	 * Generates a temp user
	 * @param int $num number of users to generate
	 * @return array
	 */
	protected function generateUsers($num = 1) {
		$users = array();
		for ($i = 0; $i < $num; $i++) {
			$user = $this->getUniqueID();
			\OC_User::createUser($user, 'password');
			$this->users[] = $user;
			$users[] = $user;
		}
		return count($users) == 1 ? reset($users) : $users;
	}

	protected function tearDown() {
		foreach($this->users as $user) {
			\OC_User::deleteUser($user);
		}

		\OC_Group::deleteGroup('admin');

		parent::tearDown();
	}
}
