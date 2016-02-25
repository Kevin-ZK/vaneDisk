<?php

namespace OCA\Provisioning_API\Tests;

class AppsTest extends TestCase {
	public function testGetAppInfo() {
		$result = \OCA\provisioning_API\Apps::getAppInfo(array('appid' => 'provisioning_api'));
		$this->assertInstanceOf('OC_OCS_Result', $result);
		$this->assertTrue($result->succeeded());

	}

	public function testGetAppInfoOnBadAppID() {

		$result = \OCA\provisioning_API\Apps::getAppInfo(array('appid' => 'not_provisioning_api'));
		$this->assertInstanceOf('OC_OCS_Result', $result);
		$this->assertFalse($result->succeeded());
		$this->assertEquals(\OCP\API::RESPOND_NOT_FOUND, $result->getStatusCode());

	}

	public function testGetApps() {

		$user = $this->generateUsers();
		\OC_Group::addToGroup($user, 'admin');
		self::loginAsUser($user);

		$result = \OCA\provisioning_API\Apps::getApps(array());

		$this->assertTrue($result->succeeded());
		$data = $result->getData();
		$this->assertEquals(count(\OC_App::listAllApps()), count($data['apps']));

	}

	public function testGetAppsEnabled() {

		$_GET['filter'] = 'enabled';
		$result = \OCA\provisioning_API\Apps::getApps(array('filter' => 'enabled'));
		$this->assertTrue($result->succeeded());
		$data = $result->getData();
		$this->assertEquals(count(\OC_App::getEnabledApps()), count($data['apps']));

	}

	public function testGetAppsDisabled() {

		$_GET['filter'] = 'disabled';
		$result = \OCA\provisioning_API\Apps::getApps(array('filter' => 'disabled'));
		$this->assertTrue($result->succeeded());
		$data = $result->getData();
		$apps = \OC_App::listAllApps();
		$list =  array();
		foreach($apps as $app) {
			$list[] = $app['id'];
		}
		$disabled = array_diff($list, \OC_App::getEnabledApps());
		$this->assertEquals(count($disabled), count($data['apps']));

	}
}
