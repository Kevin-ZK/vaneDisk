<?php

namespace Test\Files\Storage;

class SFTP extends Storage {
	private $config;

	protected function setUp() {
		parent::setUp();

		$id = $this->getUniqueID();
		$this->config = include('files_external/tests/config.sftp.php');
		if (!is_array($this->config) or !$this->config['run']) {
			$this->markTestSkipped('SFTP backend not configured');
		}
		$this->config['root'] .= '/' . $id; //make sure we have an new empty folder to work in
		$this->instance = new \OC\Files\Storage\SFTP($this->config);
		$this->instance->mkdir('/');
	}

	protected function tearDown() {
		if ($this->instance) {
			$this->instance->rmdir('/');
		}

		parent::tearDown();
	}

	/**
	 * @dataProvider configProvider
	 */
	public function testStorageId($config, $expectedStorageId) {
		$instance = new \OC\Files\Storage\SFTP($config);
		$this->assertEquals($expectedStorageId, $instance->getId());
	}

	public function configProvider() {
		return [
			[
				// no root path
				[
					'run' => true,
					'host' => 'somehost',
					'user' => 'someuser',
					'password' => 'somepassword',
					'root' => '',
				],
				'sftp::someuser@somehost//',
			],
			[
				// without leading nor trailing slash
				[
					'run' => true,
					'host' => 'somehost',
					'user' => 'someuser',
					'password' => 'somepassword',
					'root' => 'remotedir/subdir',
				],
				'sftp::someuser@somehost//remotedir/subdir/',
			],
			[
				// regular path
				[
					'run' => true,
					'host' => 'somehost',
					'user' => 'someuser',
					'password' => 'somepassword',
					'root' => '/remotedir/subdir/',
				],
				'sftp::someuser@somehost//remotedir/subdir/',
			],
			[
				// different port
				[
					'run' => true,
					'host' => 'somehost:8822',
					'user' => 'someuser',
					'password' => 'somepassword',
					'root' => 'remotedir/subdir/',
				],
				'sftp::someuser@somehost:8822//remotedir/subdir/',
			],
		];
	}
}
