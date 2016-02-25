<?php

namespace OC\Repair;

use OC\Hooks\BasicEmitter;
use OC\RepairStep;
use OCP\BackgroundJob\IJobList;

class DropOldJobs extends BasicEmitter implements RepairStep {

	/** @var IJobList */
	protected $jobList;

	/**
	 * @param IJobList $jobList
	 */
	public function __construct(IJobList $jobList) {
		$this->jobList = $jobList;
	}

	/**
	 * Returns the step's name
	 *
	 * @return string
	 */
	public function getName() {
		return 'Drop old background jobs';
	}

	/**
	 * Run repair step.
	 * Must throw exception on error.
	 *
	 * @throws \Exception in case of failure
	 */
	public function run() {
		$oldJobs = $this->oldJobs();
		foreach($oldJobs as $job) {
			if($this->jobList->has($job['class'], $job['arguments'])) {
				$this->jobList->remove($job['class'], $job['arguments']);
			}
		}
	}

	/**
	 * returns a list of old jobs as an associative array with keys 'class' and
	 * 'arguments'.
	 *
	 * @return array
	 */
	public function oldJobs() {
		return [
			['class' => 'OC_Cache_FileGlobalGC', 'arguments' => null],
			['class' => 'OC\Cache\FileGlobalGC', 'arguments' => null],
		];
	}


}
