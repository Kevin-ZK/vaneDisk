<?php
namespace OCA\Files;

use \OCA\Files\Service\TagService;

class TagServiceTest extends \Test\TestCase {

	/**
	 * @var string
	 */
	private $user;

	/**
	 * @var \OCP\Files\Folder
	 */
	private $root;

	/**
	 * @var \OCA\Files\Service\TagService
	 */
	private $tagService;

	/**
	 * @var \OCP\ITags
	 */
	private $tagger;

	protected function setUp() {
		parent::setUp();
		$this->user = $this->getUniqueId('user');
		\OC_User::createUser($this->user, 'test');
		\OC_User::setUserId($this->user);
		\OC_Util::setupFS($this->user);
		/**
		 * @var \OCP\IUser
		 */
		$user = new \OC\User\User($this->user, null);
		/**
		 * @var \OCP\IUserSession
		 */
		$userSession = $this->getMock('\OCP\IUserSession');
		$userSession->expects($this->any())
			->method('getUser')
			->withAnyParameters()
			->will($this->returnValue($user));

		$this->root = \OC::$server->getUserFolder();

		$this->tagger = \OC::$server->getTagManager()->load('files');
		$this->tagService = new TagService(
			$userSession,
			$this->tagger,
			$this->root
		);
	}

	protected function tearDown() {
		\OC_User::setUserId('');
		\OC_User::deleteUser($this->user);
	}

	public function testUpdateFileTags() {
		$tag1 = 'tag1';
		$tag2 = 'tag2';

		$subdir = $this->root->newFolder('subdir');
		$testFile = $subdir->newFile('test.txt');
		$testFile->putContent('test contents');

		$fileId = $testFile->getId();

		// set tags
		$this->tagService->updateFileTags('subdir/test.txt', array($tag1, $tag2));

		$this->assertEquals(array($fileId), $this->tagger->getIdsForTag($tag1));
		$this->assertEquals(array($fileId), $this->tagger->getIdsForTag($tag2));

		// remove tag
		$result = $this->tagService->updateFileTags('subdir/test.txt', array($tag2));
		$this->assertEquals(array(), $this->tagger->getIdsForTag($tag1));
		$this->assertEquals(array($fileId), $this->tagger->getIdsForTag($tag2));

		// clear tags
		$result = $this->tagService->updateFileTags('subdir/test.txt', array());
		$this->assertEquals(array(), $this->tagger->getIdsForTag($tag1));
		$this->assertEquals(array(), $this->tagger->getIdsForTag($tag2));

		// non-existing file
		$caught = false;
		try {
			$this->tagService->updateFileTags('subdir/unexist.txt', array($tag1));
		} catch (\OCP\Files\NotFoundException $e) {
			$caught = true;
		}
		$this->assertTrue($caught);

		$subdir->delete();
	}
}

