<?php

namespace OCP\BackgroundJob;

/**
 * Interface IJobList
 *
 * @package OCP\BackgroundJob
 * @since 7.0.0
 */
interface IJobList {
	/**
	 * Add a job to the list
	 *
	 * @param \OCP\BackgroundJob\IJob|string $job
	 * @param mixed $argument The argument to be passed to $job->run() when the job is exectured
	 * @param string $job
	 * @return void
	 * @since 7.0.0
	 */
	public function add($job, $argument = null);

	/**
	 * Remove a job from the list
	 *
	 * @param \OCP\BackgroundJob\IJob|string $job
	 * @param mixed $argument
	 * @return void
	 * @since 7.0.0
	 */
	public function remove($job, $argument = null);

	/**
	 * check if a job is in the list
	 *
	 * @param \OCP\BackgroundJob\IJob|string $job
	 * @param mixed $argument
	 * @return bool
	 * @since 7.0.0
	 */
	public function has($job, $argument);

	/**
	 * get all jobs in the list
	 *
	 * @return \OCP\BackgroundJob\IJob[]
	 * @since 7.0.0
	 */
	public function getAll();

	/**
	 * get the next job in the list
	 *
	 * @return \OCP\BackgroundJob\IJob
	 * @since 7.0.0
	 */
	public function getNext();

	/**
	 * @param int $id
	 * @return \OCP\BackgroundJob\IJob
	 * @since 7.0.0
	 */
	public function getById($id);

	/**
	 * set the job that was last ran to the current time
	 *
	 * @param \OCP\BackgroundJob\IJob $job
	 * @return void
	 * @since 7.0.0
	 */
	public function setLastJob($job);

	/**
	 * get the id of the last ran job
	 *
	 * @return int
	 * @since 7.0.0
	 */
	public function getLastJob();

	/**
	 * set the lastRun of $job to now
	 *
	 * @param \OCP\BackgroundJob\IJob $job
	 * @return void
	 * @since 7.0.0
	 */
	public function setLastRun($job);
}
