<?php

namespace OCP\Diagnostics;

/**
 * Interface IQuery
 *
 * @package OCP\Diagnostics
 * @since 8.0.0
 */
interface IQuery {
	/**
	 * @return string
	 * @since 8.0.0
	 */
	public function getSql();

	/**
	 * @return array
	 * @since 8.0.0
	 */
	public function getParams();

	/**
	 * @return float
	 * @since 8.0.0
	 */
	public function getDuration();
}
