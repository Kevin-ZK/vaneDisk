<?php
/**
 * @since 9/26/14, 9:20 AM
 * @link http:/www.clarkt.com
 * @copyright Clark Tomlinson © 2014
 *
 * This file is licensed under the Affero General Public License version 3 or later.
 * See the COPYING-README file.
 */

namespace OCA\Files_Locking\Tests;


use OC\Files\Storage\Temporary;
use OC_User;
use OC_User_Dummy;
use OCA\Files_Locking\LockingWrapper;
use OCP\App;

class LockingWrapperTest extends \Test\TestCase {

	/** @var Temporary */
	private $fileSystem;

	/** @var LockingWrapper */
	private $fileLocker;

	protected function setup() {
		parent::setUp();

		App::checkAppEnabled('files_locking');

		$this->storage = $this->getTestStorage();
	}

	public function testReadTwice() {
		$storage1 = new LockingWrapper(array('storage' => $this->fileSystem));
		$storage2 = new LockingWrapper(array('storage' => $this->fileSystem));
		$fh1 = $storage1->fopen('foo.txt', 'r');
		$fh2 = $storage2->fopen('foo.txt', 'r');
		$this->assertTrue(true);
	}


	/**
	 * @expectedException \OCP\Files\LockNotAcquiredException
	 */
	public function testWriteTwice() {
		$storage1 = new LockingWrapper(array('storage' => $this->fileSystem));
		$storage2 = new LockingWrapper(array('storage' => $this->fileSystem));
		$fh1 = $storage1->fopen('foo.txt', 'w');
		$fh2 = $storage2->fopen('foo.txt', 'r+');
	}

	/**
	 * @expectedException \OCP\Files\LockNotAcquiredException
	 */
	public function testOpenAndRead() {
		$storage1 = new LockingWrapper(array('storage' => $this->fileSystem));
		$storage2 = new LockingWrapper(array('storage' => $this->fileSystem));
		$fh1 = $storage1->fopen('foo.txt', 'r');
		$fh2 = $storage2->fopen('foo.txt', 'w');
	}

	/**
	 * @param bool $scan
	 * @return \OC\Files\Storage\Storage
	 */
	private function getTestStorage($scan = true) {
		$this->fileSystem = new Temporary(array());
		$this->fileLocker = new LockingWrapper(array('storage' => $this->fileSystem));
		$textData = "dummy file data\n";
		$imgData = file_get_contents(\OC::$SERVERROOT . '/core/img/logo.png');
		$this->fileSystem->mkdir('folder');
		$this->fileSystem->file_put_contents('foo.txt', $textData);
		$this->fileSystem->file_put_contents('foo.png', $imgData);
		$this->fileSystem->file_put_contents('folder/bar.txt', $textData);

		if ($scan) {
			$scanner = $this->fileSystem->getScanner();
			$scanner->scan('');
		}
		return $this->fileSystem;
	}

	public function testRenameAndWrite() {
		$storage1 = new LockingWrapper(array('storage' => $this->fileSystem));

		$fh = $storage1->fopen('foo.txt', 'r');
		$storage1->rename('foo.txt', 'bar.txt');
		$storage1->unlink('bar.txt');
		$this->assertTrue(true);
	}

	public function testRenameFolder() {
		$storage1 = new LockingWrapper(array('storage' => $this->fileSystem));

		$storage1->mkdir('foo');
		$storage1->rename('foo', 'bar');
		$this->assertTrue(true);
	}
}
