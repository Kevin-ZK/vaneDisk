<?php

namespace Test\Files\Storage;

class Swift extends Storage {

	private $config;

	protected function setUp() {
		parent::setUp();

		$this->config = include('files_external/tests/config.php');
		if (!is_array($this->config) or !isset($this->config['swift'])
                    or !$this->config['swift']['run']) {
			$this->markTestSkipped('OpenStack Object Storage backend not configured');
		}
		$this->instance = new \OC\Files\Storage\Swift($this->config['swift']);
	}

	protected function tearDown() {
		if ($this->instance) {
			$connection = $this->instance->getConnection();
			$container = $connection->getContainer($this->config['swift']['bucket']);

			$objects = $container->objectList();
			while($object = $objects->next()) {
				$object->setName(str_replace('#','%23',$object->getName()));
				$object->delete();
			}

			$container->delete();
		}

		parent::tearDown();
	}
}
