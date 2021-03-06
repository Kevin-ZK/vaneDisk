<?php

// use OCP namespace for all classes that are considered public.
// This means that they should be used by apps instead of the internal vanedisk classes
namespace OCP;

use \OC\BackgroundJob\JobList;

/**
 * This class provides functions to register backgroundjobs in vanedisk
 *
 * To create a new backgroundjob create a new class that inherits from either \OC\BackgroundJob\Job,
 * \OC\BackgroundJob\QueuedJob or \OC\BackgroundJob\TimedJob and register it using
 * \OCP\BackgroundJob->registerJob($job, $argument), $argument will be passed to the run() function
 * of the job when the job is executed.
 *
 * A regular Job will be executed every time cron.php is run, a QueuedJob will only run once and a TimedJob
 * will only run at a specific interval which is to be specified in the constructor of the job by calling
 * $this->setInterval($interval) with $interval in seconds.
 * @since 4.5.0
 */
class BackgroundJob {
	/**
	 * get the execution type of background jobs
	 *
	 * @return string
	 *
	 * This method returns the type how background jobs are executed. If the user
	 * did not select something, the type is ajax.
	 * @since 5.0.0
	 */
	public static function getExecutionType() {
		return \OC::$server->getConfig()->getAppValue('core', 'backgroundjobs_mode', 'ajax');
	}

	/**
	 * sets the background jobs execution type
	 *
	 * @param string $type execution type
	 * @return false|null
	 *
	 * This method sets the execution type of the background jobs. Possible types
	 * are "none", "ajax", "webcron", "cron"
	 * @since 5.0.0
	 */
	public static function setExecutionType($type) {
		if( !in_array( $type, array('none', 'ajax', 'webcron', 'cron'))) {
			return false;
		}
		\OC::$server->getConfig()->setAppValue('core', 'backgroundjobs_mode', $type);
	}

	/**
	 * @param string $job
	 * @param mixed $argument
	 * @deprecated 8.1.0 Use \OC::$server->getJobList()->add() instead
	 * @since 6.0.0
	 */
	public static function registerJob($job, $argument = null) {
		$jobList = \OC::$server->getJobList();
		$jobList->add($job, $argument);
	}

	/**
	 * @deprecated 6.0.0
	 * creates a regular task
	 * @param string $klass class name
	 * @param string $method method name
	 * @return boolean|null
	 * @since 4.5.0
	 */
	public static function addRegularTask($klass, $method) {
		if (!\OC::needUpgrade()) {
			self::registerJob('OC\BackgroundJob\Legacy\RegularJob', array($klass, $method));
			return true;
		}
	}

	/**
	 * @deprecated 6.0.0
	 * gets all regular tasks
	 * @return array
	 *
	 * key is string "$klass-$method", value is array( $klass, $method )
	 * @since 4.5.0
	 */
	static public function allRegularTasks() {
		$jobList = \OC::$server->getJobList();
		$allJobs = $jobList->getAll();
		$regularJobs = array();
		foreach ($allJobs as $job) {
			if ($job instanceof RegularLegacyJob) {
				$key = implode('-', $job->getArgument());
				$regularJobs[$key] = $job->getArgument();
			}
		}
		return $regularJobs;
	}

	/**
	 * @deprecated 6.0.0
	 * Gets one queued task
	 * @param int $id ID of the task
	 * @return BackgroundJob\IJob|null
	 * @since 4.5.0
	 */
	public static function findQueuedTask($id) {
		$jobList = \OC::$server->getJobList();
		return $jobList->getById($id);
	}

	/**
	 * @deprecated 6.0.0
	 * Gets all queued tasks
	 * @return array an array of associative arrays
	 * @since 4.5.0
	 */
	public static function allQueuedTasks() {
		$jobList = \OC::$server->getJobList();
		$allJobs = $jobList->getAll();
		$queuedJobs = array();
		foreach ($allJobs as $job) {
			if ($job instanceof QueuedLegacyJob) {
				$queuedJob = $job->getArgument();
				$queuedJob['id'] = $job->getId();
				$queuedJobs[] = $queuedJob;
			}
		}
		return $queuedJobs;
	}

	/**
	 * @deprecated 6.0.0
	 * Gets all queued tasks of a specific app
	 * @param string $app app name
	 * @return array an array of associative arrays
	 * @since 4.5.0
	 */
	public static function queuedTaskWhereAppIs($app) {
		$jobList = \OC::$server->getJobList();
		$allJobs = $jobList->getAll();
		$queuedJobs = array();
		foreach ($allJobs as $job) {
			if ($job instanceof QueuedLegacyJob) {
				$queuedJob = $job->getArgument();
				$queuedJob['id'] = $job->getId();
				if ($queuedJob['app'] === $app) {
					$queuedJobs[] = $queuedJob;
				}
			}
		}
		return $queuedJobs;
	}

	/**
	 * @deprecated 6.0.0
	 * queues a task
	 * @param string $app app name
	 * @param string $class class name
	 * @param string $method method name
	 * @param string $parameters all useful data as text
	 * @return boolean id of task
	 * @since 4.5.0
	 */
	public static function addQueuedTask($app, $class, $method, $parameters) {
		self::registerJob('OC\BackgroundJob\Legacy\QueuedJob', array('app' => $app, 'klass' => $class, 'method' => $method, 'parameters' => $parameters));
		return true;
	}

	/**
	 * @deprecated 6.0.0
	 * deletes a queued task
	 * @param int $id id of task
	 * @return boolean|null
	 *
	 * Deletes a report
	 * @since 4.5.0
	 */
	public static function deleteQueuedTask($id) {
		$jobList = \OC::$server->getJobList();
		$job = $jobList->getById($id);
		if ($job) {
			$jobList->remove($job);
		}
	}
}
