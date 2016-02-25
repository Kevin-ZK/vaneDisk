<?php
namespace OCA\Files\Command;

use Doctrine\DBAL\Platforms\PostgreSqlPlatform;
use Doctrine\DBAL\Platforms\SqlitePlatform;
use OCP\IDBConnection;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Delete all file entries that have no matching entries in the storage table.
 */
class DeleteOrphanedFiles extends Command {

	/**
	 * @var IDBConnection
	 */
	protected $connection;

	public function __construct(IDBConnection $connection) {
		$this->connection = $connection;
		parent::__construct();
	}

	protected function configure() {
		$this
			->setName('files:cleanup')
			->setDescription('cleanup filecache');
	}

	public function execute(InputInterface $input, OutputInterface $output) {

		$sql =
			'DELETE FROM `*PREFIX*filecache` ' .
			'WHERE NOT EXISTS ' .
			'(SELECT 1 FROM `*PREFIX*storages` WHERE `storage` = `numeric_id`)';

		$deletedEntries = $this->connection->executeUpdate($sql);
		$output->writeln("$deletedEntries orphaned file cache entries deleted");
	}

}
