<?php

namespace OCA\Files_sharing\Tests;
use OCA\Files_sharing\Tests\TestCase;


class Activity extends \OCA\Files_Sharing\Tests\TestCase{

	/**
	 * @var \OCA\Files_Sharing\Activity
	 */
	private $activity;

	protected function setUp() {
		parent::setUp();
		$this->activity = new \OCA\Files_Sharing\Activity(
			$this->getMock('\OC\L10N\Factory'),
			$this->getMockBuilder('\OC\URLGenerator')
				->disableOriginalConstructor()
				->getMock()
		);
	}

	/**
	 * @dataProvider dataTestGetDefaultType
	 */
	public function testGetDefaultTypes($method, $expectedResult) {
		$result = $this->activity->getDefaultTypes($method);

		if (is_array($expectedResult)) {
			$this->assertSame(count($expectedResult), count($result));
			foreach ($expectedResult as $key => $expected) {
				$this->assertSame($expected, $result[$key]);
			}
		} else {
			$this->assertSame($expectedResult, $result);
		}

	}

	public function dataTestGetDefaultType() {
		return array(
			array('email', array(\OCA\Files_Sharing\Activity::TYPE_SHARED, \OCA\Files_Sharing\Activity::TYPE_REMOTE_SHARE)),
			array('stream', array(\OCA\Files_Sharing\Activity::TYPE_SHARED, \OCA\Files_Sharing\Activity::TYPE_REMOTE_SHARE, \OCA\Files_Sharing\Activity::TYPE_PUBLIC_LINKS)),
		);
	}

}
