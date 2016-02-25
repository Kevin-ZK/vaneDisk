<?php

namespace OC\Diagnostics;

use OCP\Diagnostics\IEventLogger;

/**
 * Dummy event logger that doesn't actually log anything
 */
class NullEventLogger implements IEventLogger {
	/**
	 * Mark the start of an event
	 *
	 * @param $id
	 * @param $description
	 */
	public function start($id, $description) {
	}

	/**
	 * Mark the end of an event
	 *
	 * @param $id
	 */
	public function end($id) {
	}

	public function log($id, $description, $start, $end) {
	}

	/**
	 * @return \OCP\Diagnostics\IEvent[]
	 */
	public function getEvents() {
		return array();
	}
}
