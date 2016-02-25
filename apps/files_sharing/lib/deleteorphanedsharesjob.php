<?php

namespace OCA\Files_sharing\Lib;

use OC\BackgroundJob\TimedJob;

/**
 * Delete all share entries that have no matching entries in the file cache table.
 */
class DeleteOrphanedSharesJob extends TimedJob {

	/**
	 * Default interval in minutes
	 *
	 * @var int $defaultIntervalMin
	 **/
	protected $defaultIntervalMin = 15;

	/**
	 * sets the correct interval for this timed job
	 */
	public function __construct(){
		$this->interval = $this->defaultIntervalMin * 60;
	}

	/**
	 * Makes the background job do its work
	 *
	 * @param array $argument unused argument
	 */
	public function run($argument) {
		$connection = \OC::$server->getDatabaseConnection();
		$logger = \OC::$server->getLogger();

		$sql =
			'DELETE FROM `*PREFIX*share` ' .
			'WHERE `item_type` in (\'file\', \'folder\') ' .
			'AND NOT EXISTS (SELECT `fileid` FROM `*PREFIX*filecache` WHERE `file_source` = `fileid`)';

		$deletedEntries = $connection->executeUpdate($sql);
		$logger->debug("$deletedEntries orphaned share(s) deleted", ['app' => 'DeleteOrphanedSharesJob']);
	}

}
