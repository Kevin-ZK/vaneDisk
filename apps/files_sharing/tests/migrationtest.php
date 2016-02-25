<?php

use OCA\Files_Sharing\Tests\TestCase;
use OCA\Files_Sharing\Migration;

class MigrationTest extends TestCase {

	/**
	 * @var \OCP\IDBConnection
	 */
	private $connection;

	function __construct() {
		parent::__construct();

		$this->connection = \OC::$server->getDatabaseConnection();
	}

	function testAddAccept() {

		$query = $this->connection->prepare('
			INSERT INTO `*PREFIX*share_external`
			(`remote`, `share_token`, `password`, `name`, `owner`, `user`, `mountpoint`, `mountpoint_hash`, `remote_id`, `accepted`)
			VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
		');

		for ($i = 0; $i < 10; $i++) {
			$query->execute(array('remote', 'token', 'password', 'name', 'owner', 'user', 'mount point', $i, $i, 0));
		}

		$query = $this->connection->prepare('SELECT `id` FROM `*PREFIX*share_external`');
		$query->execute();
		$dummyEntries = $query->fetchAll();

		$this->assertSame(10, count($dummyEntries));

		$m = new Migration();
		$m->addAcceptRow();

		// verify result
		$query = $this->connection->prepare('SELECT `accepted` FROM `*PREFIX*share_external`');
		$query->execute();
		$results = $query->fetchAll();
		$this->assertSame(10, count($results));

		foreach ($results as $r) {
			$this->assertSame(1, (int) $r['accepted']);
		}

		// cleanup
		$cleanup = $this->connection->prepare('DELETE FROM `*PREFIX*share_external`');
		$cleanup->execute();
	}

}
