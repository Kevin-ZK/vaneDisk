<?php

namespace OC\BackgroundJob;
use OCP\ILogger;

/**
 * Class QueuedJob
 *
 * create a background job that is to be executed once
 *
 * @package OC\BackgroundJob
 */
abstract class QueuedJob extends Job {
	/**
	 * run the job, then remove it from the joblist
	 *
	 * @param JobList $jobList
	 * @param ILogger $logger
	 */
	public function execute($jobList, ILogger $logger = null) {
		$jobList->remove($this, $this->argument);
		parent::execute($jobList, $logger);
	}
}
