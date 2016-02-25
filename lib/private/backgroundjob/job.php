<?php

namespace OC\BackgroundJob;

use OCP\BackgroundJob\IJob;
use OCP\ILogger;

abstract class Job implements IJob {
	/**
	 * @var int $id
	 */
	protected $id;

	/**
	 * @var int $lastRun
	 */
	protected $lastRun;

	/**
	 * @var mixed $argument
	 */
	protected $argument;

	/**
	 * @param JobList $jobList
	 * @param ILogger $logger
	 */
	public function execute($jobList, ILogger $logger = null) {
		$jobList->setLastRun($this);
		try {
			$this->run($this->argument);
		} catch (\Exception $e) {
			if ($logger) {
				$logger->error('Error while running background job: ' . $e->getMessage());
			}
			$jobList->remove($this, $this->argument);
		}
	}

	abstract protected function run($argument);

	public function setId($id) {
		$this->id = $id;
	}

	public function setLastRun($lastRun) {
		$this->lastRun = $lastRun;
	}

	public function setArgument($argument) {
		$this->argument = $argument;
	}

	public function getId() {
		return $this->id;
	}

	public function getLastRun() {
		return $this->lastRun;
	}

	public function getArgument() {
		return $this->argument;
	}
}
