<?php

namespace OC\BackgroundJob;
use OCP\ILogger;

/**
 * Class QueuedJob
 *
 * create a background job that is to be executed at an interval
 *
 * @package OC\BackgroundJob
 */
abstract class TimedJob extends Job {
	protected $interval = 0;

	/**
	 * set the interval for the job
	 *
	 * @param int $interval
	 */
	public function setInterval($interval) {
		$this->interval = $interval;
	}

	/**
	 * run the job if
	 *
	 * @param JobList $jobList
	 * @param ILogger $logger
	 */
	public function execute($jobList, ILogger $logger = null) {
		if ((time() - $this->lastRun) > $this->interval) {
			parent::execute($jobList, $logger);
		}
	}
}
