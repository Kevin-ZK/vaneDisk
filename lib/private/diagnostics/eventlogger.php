<?php

namespace OC\Diagnostics;

use OCP\Diagnostics\IEventLogger;

class EventLogger implements IEventLogger {
	/**
	 * @var \OC\Diagnostics\Event[]
	 */
	private $events = array();

	public function start($id, $description) {
		$this->events[$id] = new Event($id, $description, microtime(true));
	}

	public function end($id) {
		if (isset($this->events[$id])) {
			$timing = $this->events[$id];
			$timing->end(microtime(true));
		}
	}

	public function log($id, $description, $start, $end) {
		$this->events[$id] = new Event($id, $description, $start);
		$this->events[$id]->end($end);
	}

	/**
	 * @return \OCP\Diagnostics\IEvent[]
	 */
	public function getEvents() {
		return $this->events;
	}
}
