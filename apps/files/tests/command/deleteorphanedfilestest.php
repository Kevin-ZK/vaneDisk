<?php

namespace OCA\Files\Tests\Command;

use OCA\Files\Command\DeleteOrphanedFiles;

class DeleteOrphanedFilesTest extends \Test\TestCase {

	/**
	 * @var DeleteOrphanedFiles
	 */
	private $command;

	/**
	 * @var \OCP\IDBConnection
	 */
	private $connection;

	/**
	 * @var string
	 */
	private $user1;

	protected function setup() {
		parent::setUp();

		$this->connection = \OC::$server->getDatabaseConnection();

		$this->user1 = $this->getUniqueID('user1_');

		$userManager = \OC::$server->getUserManager();
		$userManager->createUser($this->user1, 'pass');

		$this->command = new DeleteOrphanedFiles($this->connection);
	}

	protected function tearDown() {
		$userManager = \OC::$server->getUserManager();
		$user1 = $userManager->get($this->user1);
		if($user1) {
			$user1->delete();
		}

		$this->logout();

		parent::tearDown();
	}

	protected function getFile($fileId) {
		$stmt = $this->connection->executeQuery('SELECT * FROM `*PREFIX*filecache` WHERE `fileid` = ?', [$fileId]);
		return $stmt->fetchAll();
	}

	/**
	 * Test clearing orphaned files
	 */
	public function testClearFiles() {
		$input = $this->getMockBuilder('Symfony\Component\Console\Input\InputInterface')
			->disableOriginalConstructor()
			->getMock();
		$output = $this->getMockBuilder('Symfony\Component\Console\Output\OutputInterface')
			->disableOriginalConstructor()
			->getMock();

		$this->loginAsUser($this->user1);

		$view = new \OC\Files\View('/' . $this->user1 . '/');
		$view->mkdir('files/test');

		$fileInfo = $view->getFileInfo('files/test');

		$storageId = $fileInfo->getStorage()->getId();

		$this->assertCount(1, $this->getFile($fileInfo->getId()), 'Asserts that file is available');

		$this->command->execute($input, $output);

		$this->assertCount(1, $this->getFile($fileInfo->getId()), 'Asserts that file is still available');

		$deletedRows = $this->connection->executeUpdate('DELETE FROM `*PREFIX*storages` WHERE `id` = ?', [$storageId]);
		$this->assertNotNull($deletedRows, 'Asserts that storage got deleted');
		$this->assertSame(1, $deletedRows, 'Asserts that storage got deleted');

		// parent folder, `files`, ´test` and `welcome.txt` => 4 elements
		$output
			->expects($this->once())
			->method('writeln')
			->with('4 orphaned file cache entries deleted');

		$this->command->execute($input, $output);

		$this->assertCount(0, $this->getFile($fileInfo->getId()), 'Asserts that file gets cleaned up');

		$view->unlink('files/test');
	}
}

