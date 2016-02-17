<?php

namespace OCA\Activity\Tests;

use OCA\Activity\Data;
use OCP\Activity\IExtension;

class DataDeleteActivitiesTest extends TestCase {
	/** @var \OCA\Activity\Data */
	protected $data;

	protected function setUp() {
		parent::setUp();

		$activities = array(
			array('affectedUser' => 'delete', 'subject' => 'subject', 'time' => 0),
			array('affectedUser' => 'delete', 'subject' => 'subject2', 'time' => time() - 2 * 365 * 24 * 3600),
			array('affectedUser' => 'otherUser', 'subject' => 'subject', 'time' => time()),
			array('affectedUser' => 'otherUser', 'subject' => 'subject2', 'time' => time()),
		);

		$queryActivity = \OCP\DB::prepare('INSERT INTO `*PREFIX*activity`(`app`, `subject`, `subjectparams`, `message`, `messageparams`, `file`, `link`, `user`, `affecteduser`, `timestamp`, `priority`, `type`)' . ' VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ? )');
		foreach ($activities as $activity) {
			$queryActivity->execute(array(
				'app',
				$activity['subject'],
				json_encode([]),
				'',
				json_encode([]),
				'file',
				'link',
				'user',
				$activity['affectedUser'],
				$activity['time'],
				IExtension::PRIORITY_MEDIUM,
				'test',
			));
		}
		$this->data = new Data(
			$this->getMock('\OCP\Activity\IManager')
		);
	}

	protected function tearDown() {
		$this->data->deleteActivities(array(
			'type' => 'test',
		));

		parent::tearDown();
	}

	public function deleteActivitiesData() {
		return array(
			array(array('affecteduser' => 'delete'), array('otherUser')),
			array(array('affecteduser' => array('delete', '=')), array('otherUser')),
			array(array('timestamp' => array(time() - 10, '<')), array('otherUser')),
			array(array('timestamp' => array(time() - 10, '>')), array('delete')),
		);
	}

	/**
	 * @dataProvider deleteActivitiesData
	 */
	public function testDeleteActivities($condition, $expected) {
		$this->assertUserActivities(array('delete', 'otherUser'));
		$this->data->deleteActivities($condition);
		$this->assertUserActivities($expected);
	}

	public function testExpireActivities() {
		$backgroundjob = new \OCA\Activity\BackgroundJob\ExpireActivities();
		$this->assertUserActivities(array('delete', 'otherUser'));
		$jobList = $this->getMock('\OCP\BackgroundJob\IJobList');
		$backgroundjob->execute($jobList);
		$this->assertUserActivities(array('otherUser'));
	}

	protected function assertUserActivities($expected) {
		$query = \OCP\DB::prepare("SELECT `affecteduser` FROM `*PREFIX*activity` WHERE `type` = 'test'");
		$this->assertTableKeys($expected, $query, 'affecteduser');
	}

	protected function assertTableKeys($expected, \OC_DB_StatementWrapper $query, $keyName) {
		$result = $query->execute();

		$users = array();
		while ($row = $result->fetchRow()) {
			$users[] = $row[$keyName];
		}
		$users = array_unique($users);
		sort($users);
		sort($expected);

		$this->assertEquals($expected, $users);
	}
}
