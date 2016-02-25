<?php

namespace OCA\Provisioning_API\Tests;

class GroupsTest extends TestCase {
	public function testGetGroupAsUser() {

		$users = $this->generateUsers(2);
		self::loginAsUser($users[0]);

		$group = $this->getUniqueID();
		\OC_Group::createGroup($group);
		\OC_Group::addToGroup($users[1], $group);

		$result = \OCA\provisioning_api\Groups::getGroup(array(
			'groupid' => $group,
		));

		$this->assertInstanceOf('OC_OCS_Result', $result);
		$this->assertFalse($result->succeeded());
		$this->assertEquals(\OCP\API::RESPOND_UNAUTHORISED, $result->getStatusCode());

	}

	public function testGetGroupAsSubadmin() {

		$users = $this->generateUsers(2);
		self::loginAsUser($users[0]);

		$group = $this->getUniqueID();
		\OC_Group::createGroup($group);
		\OC_Group::addToGroup($users[0], $group);
		\OC_Group::addToGroup($users[1], $group);

		\OC_SubAdmin::createSubAdmin($users[0], $group);

		$result = \OCA\provisioning_api\Groups::getGroup(array(
			'groupid' => $group,
		));

		$this->assertInstanceOf('OC_OCS_Result', $result);
		$this->assertTrue($result->succeeded());
		$this->assertEquals(1, sizeof($result->getData()), 'Asserting the result data array only has the "users" key');
		$this->assertArrayHasKey('users', $result->getData());
		$resultData = $result->getData();
		$resultData = $resultData['users'];

		sort($users);
		sort($resultData);
		$this->assertEquals($users, $resultData);

	}

	public function testGetGroupAsIrrelevantSubadmin() {

		$users = $this->generateUsers(2);
		self::loginAsUser($users[0]);

		$group = $this->getUniqueID();
		\OC_Group::createGroup($group);
		$group2 = $this->getUniqueID();
		\OC_Group::createGroup($group2);
		\OC_Group::addToGroup($users[1], $group);
		\OC_Group::addToGroup($users[0], $group2);

		\OC_SubAdmin::createSubAdmin($users[0], $group2);

		$result = \OCA\provisioning_api\Groups::getGroup(array(
			'groupid' => $group,
		));

		$this->assertInstanceOf('OC_OCS_Result', $result);
		$this->assertFalse($result->succeeded());
		$this->assertEquals(\OCP\API::RESPOND_UNAUTHORISED, $result->getStatusCode());

	}

	public function testGetGroupAsAdmin() {

		$users = $this->generateUsers(2);
		self::loginAsUser($users[0]);

		$group = $this->getUniqueID();
		\OC_Group::createGroup($group);

		\OC_Group::addToGroup($users[1], $group);
		\OC_Group::addToGroup($users[0], 'admin');

		$result = \OCA\provisioning_api\Groups::getGroup(array(
			'groupid' => $group,
		));

		$this->assertInstanceOf('OC_OCS_Result', $result);
		$this->assertTrue($result->succeeded());
		$this->assertEquals(array('users' => array($users[1])), $result->getData());

	}

	public function testGetSubAdminsOfGroup() {
		$user1 = $this->generateUsers();
		$user2 = $this->generateUsers();
		self::loginAsUser($user1);
		\OC_Group::addToGroup($user1, 'admin');
		$group1 = $this->getUniqueID();
		\OC_Group::createGroup($group1);
		\OC_SubAdmin::createSubAdmin($user2, $group1);
		$result = \OCA\provisioning_api\Groups::getSubAdminsOfGroup(array(
			'groupid' => $group1,
		));
		$this->assertInstanceOf('OC_OCS_Result', $result);
		$this->assertTrue($result->succeeded());
		$data = $result->getData();
		$this->assertEquals($user2, reset($data));
		\OC_Group::deleteGroup($group1);

		$user1 = $this->generateUsers();
		self::loginAsUser($user1);
		\OC_Group::addToGroup($user1, 'admin');
		$result = \OCA\provisioning_api\Groups::getSubAdminsOfGroup(array(
			'groupid' => $this->getUniqueID(),
		));
		$this->assertInstanceOf('OC_OCS_Result', $result);
		$this->assertFalse($result->succeeded());
		$this->assertEquals(101, $result->getStatusCode());
	}
}
