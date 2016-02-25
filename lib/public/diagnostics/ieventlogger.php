<?php

namespace OCP\Diagnostics;

/**
 * Interface IEventLogger
 *
 * @package OCP\Diagnostics
 * @since 8.0.0
 */
interface IEventLogger {
	/**
	 * Mark the start of an event
	 *
	 * @param string $id
	 * @param string $description
	 * @since 8.0.0
	 */
	public function start($id, $description);

	/**
	 * Mark the end of an event
	 *
	 * @param string $id
	 * @since 8.0.0
	 */
	public function end($id);

	/**
	 * @param string $id
	 * @param string $description
	 * @param float $start
	 * @param float $end
	 * @since 8.0.0
	 */
	public function log($id, $description, $start, $end);

	/**
	 * @return \OCP\Diagnostics\IEvent[]
	 * @since 8.0.0
	 */
	public function getEvents();
}
