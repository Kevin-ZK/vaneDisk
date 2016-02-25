<?php

namespace OCA\Files_external\Tests;

use \OCA\Files_external\Lib\StorageConfig;

class StorageConfigTest extends \Test\TestCase {

	public function testJsonSerialization() {
		$storageConfig = new StorageConfig(1);
		$storageConfig->setMountPoint('test');
		$storageConfig->setBackendClass('\OC\Files\Storage\SMB');
		$storageConfig->setBackendOptions(['user' => 'test', 'password' => 'password123']);
		$storageConfig->setPriority(128);
		$storageConfig->setApplicableUsers(['user1', 'user2']);
		$storageConfig->setApplicableGroups(['group1', 'group2']);
		$storageConfig->setMountOptions(['preview' => false]);

		$json = $storageConfig->jsonSerialize();

		$this->assertEquals(1, $json['id']);
		$this->assertEquals('/test', $json['mountPoint']);
		$this->assertEquals('\OC\Files\Storage\SMB', $json['backendClass']);
		$this->assertEquals('test', $json['backendOptions']['user']);
		$this->assertEquals('password123', $json['backendOptions']['password']);
		$this->assertEquals(128, $json['priority']);
		$this->assertEquals(['user1', 'user2'], $json['applicableUsers']);
		$this->assertEquals(['group1', 'group2'], $json['applicableGroups']);
		$this->assertEquals(['preview' => false], $json['mountOptions']);
	}

}
