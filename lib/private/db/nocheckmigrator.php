<?php

namespace OC\DB;

use Doctrine\DBAL\Schema\Schema;

/**
 * migrator for database platforms that don't support the upgrade check
 *
 * @package OC\DB
 */
class NoCheckMigrator extends Migrator {
	/**
	 * @param \Doctrine\DBAL\Schema\Schema $targetSchema
	 * @throws \OC\DB\MigrationException
	 */
	public function checkMigrate(Schema $targetSchema) {}
}
