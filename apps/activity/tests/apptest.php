<?php

namespace OCA\Activity\Tests;

class AppTest extends TestCase {
	public function testNavigationEntry() {
		$navigationManager = \OC::$server->getNavigationManager();
		$navigationManager->clear();
		$this->assertEmpty($navigationManager->getAll());

		require '../appinfo/app.php';

		// Test whether the navigation entry got added
		$this->assertCount(1, $navigationManager->getAll());
	}

	public function testJobList() {
		$jobList = \OC::$server->getJobList();

		require '../appinfo/app.php';

		// Test whether the background jobs got registered
		$this->assertTrue($jobList->has('OCA\Activity\BackgroundJob\EmailNotification', null));
		$this->assertTrue($jobList->has('OCA\Activity\BackgroundJob\ExpireActivities', null));
	}

// FIXME: Uncomment once the OC_App stuff is not static anymore
//	public function testPersonalPanel() {
//		require '../appinfo/app.php';
//
//		// Test whether the personal panel got registered
//		$forms = \OC_App::getForms('personal');
//		$this->assertGreaterThanOrEqual(1, sizeof($forms), 'Expected to find the activity personal panel');
//
//		$foundActivityPanel = false;
//		foreach ($forms as $form) {
//			if (strpos($form, 'id="activity_notifications"') !== false) {
//				$foundActivityPanel = true;
//				break;
//			}
//		}
//		$this->assertTrue($foundActivityPanel, 'Expected to find the activity personal panel');
//	}
}
