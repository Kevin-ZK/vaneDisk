<?php

namespace OCA\Activity\BackgroundJob;

/**
 * Class ExpireActivities
 *
 * @package OCA\Activity\BackgroundJob
 */
class ExpireActivities extends \OC\BackgroundJob\TimedJob {
	public function __construct() {
		// Run once per day
		$this->setInterval(60 * 60 * 24);
	}

	protected function run($argument) {
		// Remove activities that are older then one year
		$expireDays = \OCP\Config::getSystemValue('activity_expire_days', 365);
		$data = new \OCA\Activity\Data(
			\OC::$server->getActivityManager()
		);
		$data->expire($expireDays);
	}
}
