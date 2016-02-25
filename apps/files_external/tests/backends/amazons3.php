<?php

namespace Test\Files\Storage;

class AmazonS3 extends Storage {

	private $config;

	protected function setUp() {
		parent::setUp();

		$this->config = include('files_external/tests/config.php');
		if ( ! is_array($this->config) or ! isset($this->config['amazons3']) or ! $this->config['amazons3']['run']) {
			$this->markTestSkipped('AmazonS3 backend not configured');
		}
		$this->instance = new \OC\Files\Storage\AmazonS3($this->config['amazons3']);
	}

	protected function tearDown() {
		if ($this->instance) {
			$this->instance->rmdir('');
		}

		parent::tearDown();
	}

	public function testStat() {
		$this->markTestSkipped('S3 doesn\'t update the parents folder mtime');
	}
}
