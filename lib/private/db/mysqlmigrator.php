<?php

namespace OC\DB;

use Doctrine\DBAL\Schema\Schema;

class MySQLMigrator extends Migrator {
	/**
	 * @param Schema $targetSchema
	 * @param \Doctrine\DBAL\Connection $connection
	 * @return \Doctrine\DBAL\Schema\SchemaDiff
	 */
	protected function getDiff(Schema $targetSchema, \Doctrine\DBAL\Connection $connection) {
		$platform = $connection->getDatabasePlatform();
		$platform->registerDoctrineTypeMapping('enum', 'string');
		$platform->registerDoctrineTypeMapping('bit', 'string');

		$schemaDiff = parent::getDiff($targetSchema, $connection);

		// identifiers need to be quoted for mysql
		foreach ($schemaDiff->changedTables as $tableDiff) {
			$tableDiff->name = $this->connection->quoteIdentifier($tableDiff->name);
			foreach ($tableDiff->changedColumns as $column) {
				$column->oldColumnName = $this->connection->quoteIdentifier($column->oldColumnName);
			}
		}

		return $schemaDiff;
	}
}
