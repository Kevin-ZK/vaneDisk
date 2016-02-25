<?php

namespace OCP\Diagnostics;

/**
 * Interface IEvent
 *
 * @package OCP\Diagnostics
 * @since 8.0.0
 */
interface IEvent {
	/**
	 * @return string
	 * @since 8.0.0
	 */
	public function getId();

	/**
	 * @return string
	 * @since 8.0.0
	 */
	public function getDescription();

	/**
	 * @return float
	 * @since 8.0.0
	 */
	public function getStart();

	/**
	 * @return float
	 * @since 8.0.0
	 */
	public function getEnd();

	/**
	 * @return float
	 * @since 8.0.0
	 */
	public function getDuration();
}
