<?php

namespace OCP;

/**
 * Interface IDateTimeZone
 *
 * @package OCP
 * @since 8.0.0
 */
interface IDateTimeZone {
	/**
	 * @param bool|int $timestamp
	 * @return \DateTimeZone
	 * @since 8.0.0 - parameter $timestamp was added in 8.1.0
	 */
	public function getTimeZone($timestamp = false);
}
