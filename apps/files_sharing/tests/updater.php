<?php

/**
 * Class Test_Files_Sharing_Updater
 */
class Test_Files_Sharing_Updater extends OCA\Files_Sharing\Tests\TestCase {

	const TEST_FOLDER_NAME = '/folder_share_updater_test';

	public static function setUpBeforeClass() {
		parent::setUpBeforeClass();
		\OCA\Files_Sharing\Helper::registerHooks();
	}

	protected function setUp() {
		parent::setUp();

		$this->folder = self::TEST_FOLDER_NAME;

		$this->filename = '/share-updater-test.txt';

		// save file with content
		$this->view->file_put_contents($this->filename, $this->data);
		$this->view->mkdir($this->folder);
		$this->view->file_put_contents($this->folder . '/' . $this->filename, $this->data);
	}

	protected function tearDown() {
		$this->view->unlink($this->filename);
		$this->view->deleteAll($this->folder);

		parent::tearDown();
	}

	/**
	 * test deletion of a folder which contains share mount points. Share mount
	 * points should be unshared before the folder gets deleted so
	 * that the mount point doesn't end up at the trash bin
	 */
	function testDeleteParentFolder() {
		$status = \OC_App::isEnabled('files_trashbin');
		\OC_App::enable('files_trashbin');

		\OCA\Files_Trashbin\Trashbin::registerHooks();

		$fileinfo = \OC\Files\Filesystem::getFileInfo($this->folder);
		$this->assertTrue($fileinfo instanceof \OC\Files\FileInfo);

		\OCP\Share::shareItem('folder', $fileinfo->getId(), \OCP\Share::SHARE_TYPE_USER, self::TEST_FILES_SHARING_API_USER2, 31);

		$this->loginHelper(self::TEST_FILES_SHARING_API_USER2);
		$view = new \OC\Files\View('/' . self::TEST_FILES_SHARING_API_USER2 . '/files');

		// check if user2 can see the shared folder
		$this->assertTrue($view->file_exists($this->folder));

		$foldersShared = \OCP\Share::getItemsSharedWith('folder');
		$this->assertSame(1, count($foldersShared));

		$view->mkdir("localFolder");
		$view->file_put_contents("localFolder/localFile.txt", "local file");

		$view->rename($this->folder, 'localFolder/' . $this->folder);

		// share mount point should now be moved to the subfolder
		$this->assertFalse($view->file_exists($this->folder));
		$this->assertTrue($view->file_exists('localFolder/' .$this->folder));

		$view->unlink('localFolder');

		$this->loginHelper(self::TEST_FILES_SHARING_API_USER2);

		// shared folder should be unshared
		$foldersShared = \OCP\Share::getItemsSharedWith('folder');
		$this->assertTrue(empty($foldersShared));

		// trashbin should contain the local file but not the mount point
		$rootView = new \OC\Files\View('/' . self::TEST_FILES_SHARING_API_USER2);
		$trashContent = \OCA\Files_Trashbin\Helper::getTrashFiles('/', self::TEST_FILES_SHARING_API_USER2);
		$this->assertSame(1, count($trashContent));
		$firstElement = reset($trashContent);
		$timestamp = $firstElement['mtime'];
		$this->assertTrue($rootView->file_exists('files_trashbin/files/localFolder.d' . $timestamp . '/localFile.txt'));
		$this->assertFalse($rootView->file_exists('files_trashbin/files/localFolder.d' . $timestamp . '/' . $this->folder));

		//cleanup
		$rootView->deleteAll('files_trashin');

		if ($status === false) {
			\OC_App::disable('files_trashbin');
		}

		\OC\Files\Filesystem::getLoader()->removeStorageWrapper('oc_trashbin');
	}

	public function shareFolderProvider() {
		return [
			['/'],
			['/my_shares'],
		];
	}

	/**
	 * if a file gets shared the etag for the recipients root should change
	 *
	 * @dataProvider shareFolderProvider
	 *
	 * @param string $shareFolder share folder to use
	 */
	public function testShareFile($shareFolder) {
		$config = \OC::$server->getConfig();
		$oldShareFolder = $config->getSystemValue('share_folder');
		$config->setSystemValue('share_folder', $shareFolder);

		$this->loginHelper(self::TEST_FILES_SHARING_API_USER2);

		$beforeShareRoot = \OC\Files\Filesystem::getFileInfo('');
		$etagBeforeShareRoot = $beforeShareRoot->getEtag();

		\OC\Files\Filesystem::mkdir($shareFolder);

		$beforeShareDir = \OC\Files\Filesystem::getFileInfo($shareFolder);
		$etagBeforeShareDir = $beforeShareDir->getEtag();

		$this->loginHelper(self::TEST_FILES_SHARING_API_USER1);
		$fileinfo = \OC\Files\Filesystem::getFileInfo($this->folder);
		$result = \OCP\Share::shareItem('folder', $fileinfo->getId(), \OCP\Share::SHARE_TYPE_USER, self::TEST_FILES_SHARING_API_USER2, 31);
		$this->assertTrue($result);

		$this->loginHelper(self::TEST_FILES_SHARING_API_USER2);

		$afterShareRoot = \OC\Files\Filesystem::getFileInfo('');
		$etagAfterShareRoot = $afterShareRoot->getEtag();

		$afterShareDir = \OC\Files\Filesystem::getFileInfo($shareFolder);
		$etagAfterShareDir = $afterShareDir->getEtag();

		$this->assertTrue(is_string($etagBeforeShareRoot));
		$this->assertTrue(is_string($etagBeforeShareDir));
		$this->assertTrue(is_string($etagAfterShareRoot));
		$this->assertTrue(is_string($etagAfterShareDir));
		$this->assertTrue($etagBeforeShareRoot !== $etagAfterShareRoot);
		$this->assertTrue($etagBeforeShareDir !== $etagAfterShareDir);

		// cleanup
		$this->loginHelper(self::TEST_FILES_SHARING_API_USER1);
		$result = \OCP\Share::unshare('folder', $fileinfo->getId(), \OCP\Share::SHARE_TYPE_USER, self::TEST_FILES_SHARING_API_USER2);
		$this->assertTrue($result);

		$config->setSystemValue('share_folder', $oldShareFolder);
	}

	/**
	 * if a folder gets renamed all children mount points should be renamed too
	 */
	function testRename() {

		$fileinfo = \OC\Files\Filesystem::getFileInfo($this->folder);
		$result = \OCP\Share::shareItem('folder', $fileinfo->getId(), \OCP\Share::SHARE_TYPE_USER, self::TEST_FILES_SHARING_API_USER2, 31);
		$this->assertTrue($result);

		$this->loginHelper(self::TEST_FILES_SHARING_API_USER2);

		// make sure that the shared folder exists
		$this->assertTrue(\OC\Files\Filesystem::file_exists($this->folder));

		\OC\Files\Filesystem::mkdir('oldTarget');
		\OC\Files\Filesystem::mkdir('oldTarget/subfolder');
		\OC\Files\Filesystem::mkdir('newTarget');

		\OC\Files\Filesystem::rename($this->folder, 'oldTarget/subfolder/' . $this->folder);

		// re-login to make sure that the new mount points are initialized
		$this->loginHelper(self::TEST_FILES_SHARING_API_USER2);

		\OC\Files\Filesystem::rename('/oldTarget', '/newTarget/oldTarget');

		// re-login to make sure that the new mount points are initialized
		$this->loginHelper(self::TEST_FILES_SHARING_API_USER2);

		$this->assertTrue(\OC\Files\Filesystem::file_exists('/newTarget/oldTarget/subfolder/' . $this->folder));

		// cleanup
		$this->loginHelper(self::TEST_FILES_SHARING_API_USER1);
		$result = \OCP\Share::unshare('folder', $fileinfo->getId(), \OCP\Share::SHARE_TYPE_USER, self::TEST_FILES_SHARING_API_USER2);
		$this->assertTrue($result);
	}

}
