<?php

namespace OCA\Activity\Tests;

use OCA\Activity\Data;
use OCA\Activity\Hooks;
use OCP\Activity\IExtension;

class HooksDeleteUserTest extends TestCase {
	protected function setUp() {
		parent::setUp();

		$activities = array(
			array('affectedUser' => 'delete', 'subject' => 'subject'),
			array('affectedUser' => 'delete', 'subject' => 'subject2'),
			array('affectedUser' => 'otherUser', 'subject' => 'subject'),
			array('affectedUser' => 'otherUser', 'subject' => 'subject2'),
		);

		$queryActivity = \OCP\DB::prepare('INSERT INTO `*PREFIX*activity`(`app`, `subject`, `subjectparams`, `message`, `messageparams`, `file`, `link`, `user`, `affecteduser`, `timestamp`, `priority`, `type`)' . ' VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ? )');
		$queryMailQueue = \OCP\DB::prepare('INSERT INTO `*PREFIX*activity_mq`(`amq_appid`, `amq_subject`, `amq_subjectparams`, `amq_affecteduser`, `amq_timestamp`, `amq_type`, `amq_latest_send`)' . ' VALUES(?, ?, ?, ?, ?, ?, ?)');
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
				time(),
				IExtension::PRIORITY_MEDIUM,
				'test',
			));
			$queryMailQueue->execute(array(
				'app',
				$activity['subject'],
				json_encode([]),
				$activity['affectedUser'],
				time(),
				'test',
				time() + 10,
			));
		}
	}

	protected function tearDown() {
		$data = new Data(
			$this->getMock('\OCP\Activity\IManager')
		);
		$data->deleteActivities(array(
			'type' => 'test',
		));
		$query = \OCP\DB::prepare("DELETE FROM `*PREFIX*activity_mq` WHERE `amq_type` = 'test'");
		$query->execute();

		parent::tearDown();
	}

	public function testHooksDeleteUser() {

		$this->assertUserActivities(array('delete', 'otherUser'));
		$this->assertUserMailQueue(array('delete', 'otherUser'));
		Hooks::deleteUser(array('uid' => 'delete'));
		$this->assertUserActivities(array('otherUser'));
		$this->assertUserMailQueue(array('otherUser'));
	}

	protected function assertUserActivities($expected) {
		$query = \OCP\DB::prepare("SELECT `affecteduser` FROM `*PREFIX*activity` WHERE `type` = 'test'");
		$this->assertTableKeys($expected, $query, 'affecteduser');
	}

	protected function assertUserMailQueue($expected) {
		$query = \OCP\DB::prepare("SELECT `amq_affecteduser` FROM `*PREFIX*activity_mq` WHERE `amq_type` = 'test'");
		$this->assertTableKeys($expected, $query, 'amq_affecteduser');
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
