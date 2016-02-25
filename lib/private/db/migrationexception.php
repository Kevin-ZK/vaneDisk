<?php

namespace OC\DB;


class MigrationException extends \Exception {
	private $table;

	public function __construct($table, $message) {
		$this->table = $table;
		parent::__construct($message);
	}

	/**
	 * @return string
	 */
	public function getTable() {
		return $this->table;
	}
}
