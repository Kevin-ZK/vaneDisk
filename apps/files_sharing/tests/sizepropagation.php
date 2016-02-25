<?php

namespace OCA\Files_sharing\Tests;

use OC\Files\View;

class SizePropagation extends TestCase {

	public function testSizePropagationWhenOwnerChangesFile() {
		$this->loginHelper(self::TEST_FILES_SHARING_API_USER1);
		$recipientView = new View('/' . self::TEST_FILES_SHARING_API_USER1 . '/files');

		$this->loginHelper(self::TEST_FILES_SHARING_API_USER2);
		$ownerView = new View('/' . self::TEST_FILES_SHARING_API_USER2 . '/files');
		$ownerView->mkdir('/sharedfolder/subfolder');
		$ownerView->file_put_contents('/sharedfolder/subfolder/foo.txt', 'bar');

		$sharedFolderInfo = $ownerView->getFileInfo('/sharedfolder', false);
		\OCP\Share::shareItem('folder', $sharedFolderInfo->getId(), \OCP\Share::SHARE_TYPE_USER, self::TEST_FILES_SHARING_API_USER1, 31);
		$ownerRootInfo = $ownerView->getFileInfo('', false);

		$this->loginHelper(self::TEST_FILES_SHARING_API_USER1);
		$this->assertTrue($recipientView->file_exists('/sharedfolder/subfolder/foo.txt'));
		$recipientRootInfo = $recipientView->getFileInfo('', false);

		// when file changed as owner
		$this->loginHelper(self::TEST_FILES_SHARING_API_USER2);
		$ownerView->file_put_contents('/sharedfolder/subfolder/foo.txt', 'foobar');

		// size of recipient's root stays the same
		$this->loginHelper(self::TEST_FILES_SHARING_API_USER1);
		$newRecipientRootInfo = $recipientView->getFileInfo('', false);
		$this->assertEquals($recipientRootInfo->getSize(), $newRecipientRootInfo->getSize());

		// size of owner's root increases
		$this->loginHelper(self::TEST_FILES_SHARING_API_USER2);
		$newOwnerRootInfo = $ownerView->getFileInfo('', false);
		$this->assertEquals($ownerRootInfo->getSize() + 3, $newOwnerRootInfo->getSize());
	}

	public function testSizePropagationWhenRecipientChangesFile() {
		$this->loginHelper(self::TEST_FILES_SHARING_API_USER1);
		$recipientView = new View('/' . self::TEST_FILES_SHARING_API_USER1 . '/files');

		$this->loginHelper(self::TEST_FILES_SHARING_API_USER2);
		$ownerView = new View('/' . self::TEST_FILES_SHARING_API_USER2 . '/files');
		$ownerView->mkdir('/sharedfolder/subfolder');
		$ownerView->file_put_contents('/sharedfolder/subfolder/foo.txt', 'bar');

		$sharedFolderInfo = $ownerView->getFileInfo('/sharedfolder', false);
		\OCP\Share::shareItem('folder', $sharedFolderInfo->getId(), \OCP\Share::SHARE_TYPE_USER, self::TEST_FILES_SHARING_API_USER1, 31);
		$ownerRootInfo = $ownerView->getFileInfo('', false);

		$this->loginHelper(self::TEST_FILES_SHARING_API_USER1);
		$this->assertTrue($recipientView->file_exists('/sharedfolder/subfolder/foo.txt'));
		$recipientRootInfo = $recipientView->getFileInfo('', false);

		// when file changed as recipient
		$recipientView->file_put_contents('/sharedfolder/subfolder/foo.txt', 'foobar');

		// size of recipient's root stays the same
		$newRecipientRootInfo = $recipientView->getFileInfo('', false);
		$this->assertEquals($recipientRootInfo->getSize(), $newRecipientRootInfo->getSize());

		// size of owner's root increases
		$this->loginHelper(self::TEST_FILES_SHARING_API_USER2);
		$newOwnerRootInfo = $ownerView->getFileInfo('', false);
		$this->assertEquals($ownerRootInfo->getSize() + 3, $newOwnerRootInfo->getSize());
	}
}
