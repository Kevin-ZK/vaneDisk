<?php

namespace OCA\Files_Locking\Tests;


use OCA\Files_Locking\Lock;

class LockTest extends \Test\TestCase {
	/**
	 * @var Lock
	 */
	private $fileLock;

	protected function setup() {
		parent::setUp();

		\OCP\App::checkAppEnabled('files_locking');
		$this->fileLock = new Lock(__DIR__ . '/data/test.txt');
	}

	protected function tearDown() {
		$this->fileLock->releaseAll();
		parent::tearDown();
	}

	public function testObtainReadLockAndRelease() {
		$this->assertTrue(\Test_Helper::invokePrivate($this->fileLock, 'obtainReadLock'));
		$this->assertTrue($this->fileLock->release('read'));
	}

	public function testObtainWriteLockAndRelease() {
		$this->assertTrue(\Test_Helper::invokePrivate($this->fileLock, 'obtainWriteLock'));
		$this->assertTrue($this->fileLock->release('write'));
	}

	public function testLockLockFile() {
		$this->assertTrue(\Test_Helper::invokePrivate($this->fileLock, 'lockLockFile', array('test.txt')));
	}

	public function testReleaseAll() {
		$this->assertTrue(\Test_Helper::invokePrivate($this->fileLock, 'releaseAll'));
	}


	/**
	 * @expectedException \OCP\Files\LockNotAcquiredException
	 */
	public function testDoubleLock() {
		$lock1 = $this->fileLock;
		$lock2 = new Lock(__DIR__ . '/data/test.txt');
		$lock1->addLock(Lock::WRITE);
		$lock2->addLock(Lock::WRITE);
	}

	/**
	 * @expectedException \OCP\Files\LockNotAcquiredException
	 */
	public function testReadAfterWrite() {
		$this->fileLock->addLock(Lock::WRITE);
		$this->fileLock->addLock(Lock::READ);
	}

	private function lockExistingHandleAndOutOfScope() {
		$handle = fopen(__DIR__ . '/data/test.txt', 'c');
		$this->fileLock->addLock(Lock::WRITE, $handle);
	}

	public function testExistingHandleDontKeepLock() {
		// if the locked file handle goes out of scope, the lock needs to be cleaned up and we should be able to re-acquire a lock
		$this->lockExistingHandleAndOutOfScope();
		$lock = new Lock(__DIR__ . '/data/test.txt');
		$lock->addLock(Lock::WRITE);
		$this->assertTrue(true);
	}
}
