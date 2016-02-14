<?php

/**
 * ownCloud - Activity App
 *
 * @copyright 2014 Joas Schilling nickvergessen@owncloud.com
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU AFFERO GENERAL PUBLIC LICENSE
 * License as published by the Free Software Foundation; either
 * version 3 of the License, or any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU AFFERO GENERAL PUBLIC LICENSE for more details.
 *
 * You should have received a copy of the GNU Affero General Public
 * License along with this library.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace OCA\Activity\Tests;

use OC\ActivityManager;
use OCA\Activity\Consumer;
use OCP\Activity\IConsumer;
use OCP\DB;

class ConsumerTest extends TestCase {
	/** @var \OCA\Activity\Consumer */
	protected $consumer;

	/** @var \OCA\Activity\UserSettings */
	protected $userSettings;

	protected function setUp() {
		parent::setUp();
		$this->deleteTestActivities();

		$this->userSettings = $this->getMockBuilder('OCA\Activity\UserSettings')
			->setMethods(array('getUserSetting'))
			->disableOriginalConstructor()
			->getMock();

		$this->userSettings->expects($this->any())
			->method('getUserSetting')
			->with($this->stringContains('affectedUser'), $this->anything(), $this->anything())
			->will($this->returnValueMap(array(
				array('affectedUser', 'stream', 'type', true),
				array('affectedUser2', 'stream', 'type', true),
				array('affectedUser', 'setting', 'self', true),
				array('affectedUser2', 'setting', 'self', false),
				array('affectedUser', 'email', 'type', true),
				array('affectedUser2', 'email', 'type', true),
				array('affectedUser', 'setting', 'selfemail', true),
				array('affectedUser2', 'setting', 'selfemail', false),
				array('affectedUser', 'setting', 'batchtime', 10),
				array('affectedUser2', 'setting', 'batchtime', 10),
			)));
	}

	protected function tearDown() {
		$this->deleteTestActivities();
		parent::tearDown();
	}

	protected function deleteTestActivities() {
		$query = DB::prepare('DELETE FROM `*PREFIX*activity` WHERE `app` = ?');
		$query->execute(array('test'));
		$query = DB::prepare('DELETE FROM `*PREFIX*activity_mq` WHERE `amq_appid` = ?');
		$query->execute(array('test'));
	}

	public function receiveData() {
		return [
			['type', 'author', 'affectedUser', 'subject', 'affectedUser'],
			['type2', 'author', 'affectedUser', 'subject', false],

			['type', 'author', 'affectedUser', 'subject_self', 'affectedUser'],
			['type', 'author', 'affectedUser2', 'subject_self', 'affectedUser2'],
			['type', 'author', 'affectedUser', 'subject2', 'affectedUser'],
			['type', 'author', 'affectedUser2', 'subject2', 'affectedUser2'],

			['type', 'affectedUser', 'affectedUser', 'subject_self', 'affectedUser'],
			['type', 'affectedUser2', 'affectedUser2', 'subject_self', false],
			['type', 'affectedUser', 'affectedUser', 'subject2', 'affectedUser'],
			['type', 'affectedUser2', 'affectedUser2', 'subject2', false],
		];
	}

	/**
	 * @dataProvider receiveData
	 *
	 * @param string $type
	 * @param string $author
	 * @param string $affectedUser
	 * @param string $subject
	 * @param array|false $expected
	 */
	public function testReceiveStream($type, $author, $affectedUser, $subject, $expected) {
		$consumer = new Consumer($this->userSettings, $author);
		$consumer->receive('test', $subject, ['subjectParam1', 'subjectParam2'], 'message', ['messageParam1', 'messageParam2'], 'file', 'link', $affectedUser, $type, \OCP\Activity\IExtension::PRIORITY_HIGH);

		$query = DB::prepare("SELECT `affecteduser` FROM `*PREFIX*activity` WHERE `app` = 'test'");
		$result = $query->execute();
		$this->assertEquals($expected, $result->fetchOne(0));
		$this->assertEquals(false, $result->fetchRow());
	}

	/**
	 * @dataProvider receiveData
	 *
	 * @param string $type
	 * @param string $author
	 * @param string $affectedUser
	 * @param string $subject
	 * @param array|false $expected
	 */
	public function testReceiveEmail($type, $author, $affectedUser, $subject, $expected) {
		$consumer = new Consumer($this->userSettings, $author);
		$consumer->receive('test', $subject, ['subjectParam1', 'subjectParam2'], 'message', ['messageParam1', 'messageParam2'], 'file', 'link', $affectedUser, $type, \OCP\Activity\IExtension::PRIORITY_HIGH);

		$query = DB::prepare("SELECT `amq_affecteduser` FROM `*PREFIX*activity_mq` WHERE `amq_appid` = 'test'");
		$result = $query->execute();
		$this->assertEquals($expected, $result->fetchOne(0));
		$this->assertEquals(false, $result->fetchRow());
	}

	/**
	 * @dataProvider receiveData
	 *
	 * @param string $type
	 * @param string $author
	 * @param string $affectedUser
	 * @param string $subject
	 * @param array|false $expected
	 */
	public function testRegister($type, $author, $affectedUser, $subject, $expected) {
		$activityManager = new ActivityManager(
			$this->getMock('OCP\IRequest'),
			$this->getMock('OCP\IUserSession'),
			$this->getMock('OCP\IConfig')
		);
		$consumer = new Consumer($this->userSettings, $author);
		$container = $this->getMock('\OCP\AppFramework\IAppContainer');
		$container->expects($this->any())
			->method('query')
			->with($this->stringContains('Consumer'))
			->will($this->returnValueMap(array(
				array('Consumer', $consumer),
			)));

		Consumer::register($activityManager, $container);
		$activityManager->publishActivity('test', $subject, ['subjectParam1', 'subjectParam2'], 'message', ['messageParam1', 'messageParam2'], 'file', 'link', $affectedUser, $type, \OCP\Activity\IExtension::PRIORITY_HIGH);

		$query = DB::prepare("SELECT `affecteduser` FROM `*PREFIX*activity` WHERE `app` = 'test'");
		$result = $query->execute();
		$this->assertEquals($expected, $result->fetchOne(0));
		$this->assertEquals(false, $result->fetchRow());
	}
}
