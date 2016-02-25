<?php

namespace OCA\Files_sharing\Tests;

use OC\Files\Filesystem;
use OC\Files\View;
use OC\Lock\MemcacheLockingProvider;
use OCP\Lock\ILockingProvider;

class Locking extends TestCase {
	/**
	 * @var \OC_User_Dummy
	 */
	private $userBackend;

	private $ownerUid;
	private $recipientUid;

	public function setUp() {
		parent::setUp();

		$this->userBackend = new \OC_User_Dummy();
		\OC::$server->getUserManager()->registerBackend($this->userBackend);

		$this->ownerUid = $this->getUniqueID('owner_');
		$this->recipientUid = $this->getUniqueID('recipient_');
		$this->userBackend->createUser($this->ownerUid, '');
		$this->userBackend->createUser($this->recipientUid, '');

		$this->loginAsUser($this->ownerUid);
		Filesystem::mkdir('/foo');
		Filesystem::file_put_contents('/foo/bar.txt', 'asd');
		$fileId = Filesystem::getFileInfo('/foo/bar.txt')->getId();

		\OCP\Share::shareItem('file', $fileId, \OCP\Share::SHARE_TYPE_USER, $this->recipientUid, 31);

		$this->loginAsUser($this->recipientUid);
		$this->assertTrue(Filesystem::file_exists('bar.txt'));
	}

	public function tearDown() {
		\OC::$server->getUserManager()->removeBackend($this->userBackend);
		parent::tearDown();
	}

	/**
	 * @expectedException \OCP\Lock\LockedException
	 */
	public function testLockAsRecipient() {
		$this->loginAsUser($this->ownerUid);

		Filesystem::initMountPoints($this->recipientUid);
		$recipientView = new View('/' . $this->recipientUid . '/files');
		$recipientView->lockFile('bar.txt', ILockingProvider::LOCK_EXCLUSIVE);

		Filesystem::rename('/foo', '/asd');
	}

	public function testUnLockAsRecipient() {
		$this->loginAsUser($this->ownerUid);

		Filesystem::initMountPoints($this->recipientUid);
		$recipientView = new View('/' . $this->recipientUid . '/files');
		$recipientView->lockFile('bar.txt', ILockingProvider::LOCK_EXCLUSIVE);
		$recipientView->unlockFile('bar.txt', ILockingProvider::LOCK_EXCLUSIVE);

		$this->assertTrue(Filesystem::rename('/foo', '/asd'));
	}

	public function testChangeLock() {

		Filesystem::initMountPoints($this->recipientUid);
		$recipientView = new View('/' . $this->recipientUid . '/files');
		$recipientView->lockFile('bar.txt', ILockingProvider::LOCK_SHARED);
		$recipientView->changeLock('bar.txt', ILockingProvider::LOCK_EXCLUSIVE);
		$recipientView->unlockFile('bar.txt', ILockingProvider::LOCK_EXCLUSIVE);

		$this->assertTrue(true);
	}
}
