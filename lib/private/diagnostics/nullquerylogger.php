<?php

namespace OC\Diagnostics;

use OCP\Diagnostics\IQueryLogger;

class NullQueryLogger implements IQueryLogger {
	/**
	 * @param string $sql
	 * @param array $params
	 * @param array $types
	 */
	public function startQuery($sql, array $params = null, array $types = null) {
	}

	public function stopQuery() {
	}

	/**
	 * @return \OCP\Diagnostics\IQuery[]
	 */
	public function getQueries() {
		return array();
	}
}
