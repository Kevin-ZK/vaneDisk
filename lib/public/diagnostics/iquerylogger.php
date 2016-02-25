<?php

namespace OCP\Diagnostics;

use Doctrine\DBAL\Logging\SQLLogger;

/**
 * Interface IQueryLogger
 *
 * @package OCP\Diagnostics
 * @since 8.0.0
 */
interface IQueryLogger extends SQLLogger {
	/**
	 * @param string $sql
	 * @param array $params
	 * @param array $types
	 * @since 8.0.0
	 */
	public function startQuery($sql, array $params = null, array $types = null);

	/**
	 * @return mixed
	 * @since 8.0.0
	 */
	public function stopQuery();

	/**
	 * @return \OCP\Diagnostics\IQuery[]
	 * @since 8.0.0
	 */
	public function getQueries();
}
