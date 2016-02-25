<?php

namespace Test\Files\Storage;

class SMB extends Storage {

	protected function setUp() {
		parent::setUp();

		$id = $this->getUniqueID();
		$config = include('files_external/tests/config.smb.php');
		if (!is_array($config) or !$config['run']) {
			$this->markTestSkipped('Samba backend not configured');
		}
		if (substr($config['root'], -1, 1) != '/') {
			$config['root'] .= '/';
		}
		$config['root'] .= $id; //make sure we have an new empty folder to work in
		$this->instance = new \OC\Files\Storage\SMB($config);
		$this->instance->mkdir('/');
	}

	protected function tearDown() {
		if ($this->instance) {
			$this->instance->rmdir('');
		}

		parent::tearDown();
	}

	public function directoryProvider() {
		// doesn't support leading/trailing spaces
		return array(array('folder'));
	}

	public function testRenameWithSpaces() {
		$this->instance->mkdir('with spaces');
		$result = $this->instance->rename('with spaces', 'foo bar');
		$this->assertTrue($result);
		$this->assertTrue($this->instance->is_dir('foo bar'));
	}

	public function testStorageId() {
		$this->instance = new \OC\Files\Storage\SMB([
			'host' => 'testhost',
			'user' => 'testuser',
			'password' => 'somepass',
			'share' => 'someshare',
			'root' => 'someroot',
		]);
		$this->assertEquals('smb::testuser@testhost//someshare//someroot/', $this->instance->getId());
		$this->instance = null;
	}
}
