<?php

namespace Test\Files\Storage;

require_once 'files_external/lib/google.php';

class Google extends Storage {

	private $config;

	protected function setUp() {
		parent::setUp();

		$this->config = include('files_external/tests/config.php');
		if (!is_array($this->config) || !isset($this->config['google'])
			|| !$this->config['google']['run']
		) {
			$this->markTestSkipped('Google Drive backend not configured');
		}
		$this->instance = new \OC\Files\Storage\Google($this->config['google']);
	}

	protected function tearDown() {
		if ($this->instance) {
			$this->instance->rmdir('/');
		}

		parent::tearDown();
	}
}
