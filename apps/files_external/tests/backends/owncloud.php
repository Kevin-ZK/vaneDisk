<?php

namespace Test\Files\Storage;

class OwnCloud extends Storage {

	private $config;

	protected function setUp() {
		parent::setUp();

		$id = $this->getUniqueID();
		$this->config = include('files_external/tests/config.php');
		if ( ! is_array($this->config) or ! isset($this->config['owncloud']) or ! $this->config['owncloud']['run']) {
			$this->markTestSkipped('ownCloud backend not configured');
		}
		$this->config['owncloud']['root'] .= '/' . $id; //make sure we have an new empty folder to work in
		$this->instance = new \OC\Files\Storage\OwnCloud($this->config['owncloud']);
		$this->instance->mkdir('/');
	}

	protected function tearDown() {
		if ($this->instance) {
			$this->instance->rmdir('/');
		}

		parent::tearDown();
	}
}
