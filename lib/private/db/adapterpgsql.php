<?php

namespace OC\DB;

class AdapterPgSql extends Adapter {
	public function lastInsertId($table) {
		return $this->conn->fetchColumn('SELECT lastval()');
	}

	const UNIX_TIMESTAMP_REPLACEMENT = 'cast(extract(epoch from current_timestamp) as integer)';
	public function fixupStatement($statement) {
		$statement = str_replace( '`', '"', $statement );
		$statement = str_ireplace( 'UNIX_TIMESTAMP()', self::UNIX_TIMESTAMP_REPLACEMENT, $statement );
		return $statement;
	}
}
