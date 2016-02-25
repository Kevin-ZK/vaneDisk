<?php
namespace OC;

/**
 * Repair step
 */
interface RepairStep {

	/**
	 * Returns the step's name
	 *
	 * @return string
	 */
	public function getName();

	/**
	 * Run repair step.
	 * Must throw exception on error.
	 *
	 * @throws \Exception in case of failure
	 */
	public function run();

}
