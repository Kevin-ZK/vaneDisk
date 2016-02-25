<?php

namespace OC\DB;

class AdapterMySQL extends Adapter {
	public function fixupStatement($statement) {
		$statement = str_replace(' ILIKE ', ' COLLATE utf8_general_ci LIKE ', $statement);
		return $statement;
	}
}
