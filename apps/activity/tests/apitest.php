<?php

namespace OCA\Activity\Tests;

use OC\ActivityManager;
use OCA\Activity\Data;
use OCA\Activity\Tests\Mock\Extension;
use OCP\Activity\IExtension;

class ApiTest extends TestCase {
	protected $originalWEBROOT;

	protected function setUp() {
		parent::setUp();

		$this->originalWEBROOT = \OC::$WEBROOT;
		\OC::$WEBROOT = '';
		\OC::$server->getUserManager()->createUser('activity-api-user1', 'activity-api-user1');
		\OC::$server->getUserManager()->createUser('activity-api-user2', 'activity-api-user2');

		$activities = array(
			array(
				'affectedUser' => 'activity-api-user1',
				'subject' => 'subject1',
				'subjectparams' => array('/A/B.txt'),
				'type' => 'type1',
			),
			array(
				'affectedUser' => 'activity-api-user1',
				'subject' => 'subject2',
				'subjectparams' => array('/A/B.txt', 'User'),
				'type' => 'type2',
			),
		);

		$queryActivity = \OCP\DB::prepare('INSERT INTO `*PREFIX*activity`(`app`, `subject`, `subjectparams`, `message`, `messageparams`, `file`, `link`, `user`, `affecteduser`, `timestamp`, `priority`, `type`)' . ' VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ? )');
		$loop = 0;
		foreach ($activities as $activity) {
			$queryActivity->execute(array(
				'app1',
				$activity['subject'],
				json_encode($activity['subjectparams']),
				'',
				json_encode([]),
				'file',
				'link',
				'user',
				$activity['affectedUser'],
				time() + $loop,
				IExtension::PRIORITY_MEDIUM,
				$activity['type'],
			));
			$loop++;
		}
	}

	protected function tearDown() {
		$data = new Data(
			$this->getMock('\OCP\Activity\IManager')
		);

		$this->deleteUser($data, 'activity-api-user1');
		$this->deleteUser($data, 'activity-api-user2');

		$data->deleteActivities(array(
			'app' => 'app1',
		));
		\OC::$WEBROOT = $this->originalWEBROOT;

		parent::tearDown();
	}

	protected function deleteUser(Data $data, $uid) {
		$data->deleteActivities(array(
			'affecteduser' => $uid,
		));
		$user = \OC::$server->getUserManager()->get($uid);
		if ($user) {
			$user->delete();
		}
	}

	public function getData() {
		return array(
			array('activity-api-user2', 0, 30, array()),
			array('activity-api-user1', 0, 30, array(
				array(
					'link' => 'link',
					'file' => 'file',
					'date' => null,
					'id' => null,
					'message' => '',
					'subject' => 'Subject2 @User #A/B.txt',
				),
				array(
					'link' => 'link',
					'file' => 'file',
					'date' => null,
					'id' => null,
					'message' => '',
					'subject' => 'Subject1 #A/B.txt',
				),
			)),
			array('activity-api-user1', 0, 1, array(
				array(
					'link' => 'link',
					'file' => 'file',
					'date' => null,
					'id' => null,
					'message' => '',
					'subject' => 'Subject2 @User #A/B.txt',
				),
			)),
			array('activity-api-user1', 1, 1, array(
				array(
					'link' => 'link',
					'file' => 'file',
					'date' => null,
					'id' => null,
					'message' => '',
					'subject' => 'Subject1 #A/B.txt',
				),
			)),
		);
	}

	/**
	 * @dataProvider getData
	 */
	public function testGet($user, $start, $count, $expected) {
		$_GET['start'] = $start;
		$_GET['count'] = $count;
		\OC_User::setUserId($user);
		$sessionUser = \OC::$server->getUserSession()->getUser();
		$this->assertInstanceOf('OCP\IUser', $sessionUser);
		$this->assertEquals($user, $sessionUser->getUID());

		$activityManager = new ActivityManager(
			$this->getMock('OCP\IRequest'),
			$this->getMock('OCP\IUserSession'),
			$this->getMock('OCP\IConfig')
		);
		$activityManager->registerExtension(function() {
			return new Extension(\OCP\Util::getL10N('activity', 'en'), $this->getMock('\OCP\IURLGenerator'));
		});
		$this->registerActivityManager($activityManager);
		$result = \OCA\Activity\Api::get(array('_route' => 'get_cloud_activity'));
		$this->registerActivityManager($this->oldManager);

		$this->assertEquals(100, $result->getStatusCode());
		$data = $result->getData();
		$this->assertEquals(sizeof($expected), sizeof($data));

		while (!empty($expected)) {
			$assertExpected = array_shift($expected);
			$assertData = array_shift($data);
			foreach ($assertExpected as $key => $value) {
				$this->assertArrayHasKey($key, $assertData);
				if ($value !== null) {
					$this->assertEquals($value, $assertData[$key]);
				}
			}
		}
	}

	/** @var \OCP\Activity\IManager */
	protected $oldManager;

	/**
	 * Register an mock for testing purposes.
	 * @param \OCP\Activity\IManager $manager
	 */
	protected function registerActivityManager(\OCP\Activity\IManager $manager) {
		$this->oldManager = \OC::$server->query('ActivityManager');
		\OC::$server->registerService('ActivityManager', function () use ($manager) {
			return $manager;
		});
	}
}
