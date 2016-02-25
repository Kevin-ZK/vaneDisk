<?php

namespace OCA\Files_sharing\Tests;

use OCA\Files\Share;

class UnshareChildren extends TestCase {

	protected $subsubfolder;

	const TEST_FOLDER_NAME = '/folder_share_api_test';

	private static $tempStorage;

	protected function setUp() {
		parent::setUp();

		\OCP\Util::connectHook('OC_Filesystem', 'post_delete', '\OCA\Files_Sharing\Hooks', 'unshareChildren');

		$this->folder = self::TEST_FOLDER_NAME;
		$this->subfolder = '/subfolder_share_api_test';
		$this->subsubfolder = '/subsubfolder_share_api_test';

		$this->filename = '/share-api-test';

		// save file with content
		$this->view->mkdir($this->folder);
		$this->view->mkdir($this->folder . $this->subfolder);
		$this->view->mkdir($this->folder . $this->subfolder . $this->subsubfolder);
		$this->view->file_put_contents($this->folder . $this->filename, $this->data);
		$this->view->file_put_contents($this->folder . $this->subfolder . $this->filename, $this->data);
	}

	protected function tearDown() {
		if ($this->view) {
			$this->view->deleteAll($this->folder);
		}

		self::$tempStorage = null;

		parent::tearDown();
	}

	/**
	 * @medium
	 */
	function testUnshareChildren() {

		$fileInfo2 = \OC\Files\Filesystem::getFileInfo($this->folder);

		$result = \OCP\Share::shareItem('folder', $fileInfo2->getId(), \OCP\Share::SHARE_TYPE_USER, self::TEST_FILES_SHARING_API_USER2, 31);
		$this->assertTrue($result);

		self::loginHelper(self::TEST_FILES_SHARING_API_USER2);

		// one folder should be shared with the user
		$sharedFolders = \OCP\Share::getItemsSharedWith('folder');
		$this->assertSame(1, count($sharedFolders));

		// move shared folder to 'localDir'
		\OC\Files\Filesystem::mkdir('localDir');
		$result = \OC\Files\Filesystem::rename($this->folder, '/localDir/' . $this->folder);
		$this->assertTrue($result);

		\OC\Files\Filesystem::unlink('localDir');

		self::loginHelper(self::TEST_FILES_SHARING_API_USER2);

		// after the parent directory was deleted the share should be unshared
		$sharedFolders = \OCP\Share::getItemsSharedWith('folder');
		$this->assertTrue(empty($sharedFolders));

		self::loginHelper(self::TEST_FILES_SHARING_API_USER1);

		// the folder for the owner should still exists
		$this->assertTrue(\OC\Files\Filesystem::file_exists($this->folder));
	}
}
