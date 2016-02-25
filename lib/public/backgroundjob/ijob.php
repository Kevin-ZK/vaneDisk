<?php

namespace OCP\BackgroundJob;
use OCP\ILogger;

/**
 * Interface IJob
 *
 * @package OCP\BackgroundJob
 * @since 7.0.0
 */
interface IJob {
	/**
	 * Run the background job with the registered argument
	 *
	 * @param \OCP\BackgroundJob\IJobList $jobList The job list that manages the state of this job
	 * @param ILogger $logger
	 * @return void
	 * @since 7.0.0
	 */
	public function execute($jobList, ILogger $logger = null);

	/**
	 * Get the id of the background job
	 * This id is determined by the job list when a job is added to the list
	 *
	 * @return int
	 * @since 7.0.0
	 */
	public function getId();

	/**
	 * Get the last time this job was run as unix timestamp
	 *
	 * @return int
	 * @since 7.0.0
	 */
	public function getLastRun();

	/**
	 * Get the argument associated with the background job
	 * This is the argument that will be passed to the background job
	 *
	 * @return mixed
	 * @since 7.0.0
	 */
	public function getArgument();
}
