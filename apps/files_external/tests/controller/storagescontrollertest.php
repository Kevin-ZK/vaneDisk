<?php
/**
 *
 * @copyright Copyright (c) 2015, ownCloud, Inc.
 * @license AGPL-3.0
 *
 * This code is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License, version 3,
 * as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License, version 3,
 * along with this program.  If not, see <http://www.gnu.org/licenses/>
 *
 */
namespace OCA\Files_external\Tests\Controller;

use \OCP\AppFramework\Http;

use \OCA\Files_external\Controller\GlobalStoragesController;
use \OCA\Files_external\Service\GlobalStoragesService;
use \OCA\Files_external\Lib\StorageConfig;
use \OCA\Files_external\NotFoundException;

abstract class StoragesControllerTest extends \Test\TestCase {

	/**
	 * @var GlobalStoragesController
	 */
	protected $controller;

	/**
	 * @var GlobalStoragesService
	 */
	protected $service;

	public function setUp() {
		\OC_Mount_Config::$skipTest = true;
	}

	public function tearDown() {
		\OC_Mount_Config::$skipTest = false;
	}

	public function testAddStorage() {
		$storageConfig = new StorageConfig(1);
		$storageConfig->setMountPoint('mount');

		$this->service->expects($this->once())
			->method('addStorage')
			->will($this->returnValue($storageConfig));

		$response = $this->controller->create(
			'mount',
			'\OC\Files\Storage\SMB',
			array(),
			[],
			[],
			[],
			null
		);

		$data = $response->getData();
		$this->assertEquals($storageConfig, $data);
		$this->assertEquals(Http::STATUS_CREATED, $response->getStatus());
	}

	public function testUpdateStorage() {
		$storageConfig = new StorageConfig(1);
		$storageConfig->setMountPoint('mount');

		$this->service->expects($this->once())
			->method('updateStorage')
			->will($this->returnValue($storageConfig));

		$response = $this->controller->update(
			1,
			'mount',
			'\OC\Files\Storage\SMB',
			array(),
			[],
			[],
			[],
			null
		);

		$data = $response->getData();
		$this->assertEquals($storageConfig, $data);
		$this->assertEquals(Http::STATUS_OK, $response->getStatus());
	}

	function mountPointNamesProvider() {
		return array(
			array(''),
			array('/'),
			array('//'),
		);
	}

	/**
	 * @dataProvider mountPointNamesProvider
	 */
	public function testAddOrUpdateStorageInvalidMountPoint($mountPoint) {
		$this->service->expects($this->never())
			->method('addStorage');
		$this->service->expects($this->never())
			->method('updateStorage');

		$response = $this->controller->create(
			$mountPoint,
			'\OC\Files\Storage\SMB',
			array(),
			[],
			[],
			[],
			null
		);

		$this->assertEquals(Http::STATUS_UNPROCESSABLE_ENTITY, $response->getStatus());

		$response = $this->controller->update(
			1,
			$mountPoint,
			'\OC\Files\Storage\SMB',
			array(),
			[],
			[],
			[],
			null
		);

		$this->assertEquals(Http::STATUS_UNPROCESSABLE_ENTITY, $response->getStatus());
	}

	public function testAddOrUpdateStorageInvalidBackend() {
		$this->service->expects($this->never())
			->method('addStorage');
		$this->service->expects($this->never())
			->method('updateStorage');

		$response = $this->controller->create(
			'mount',
			'\OC\Files\Storage\InvalidStorage',
			array(),
			[],
			[],
			[],
			null
		);

		$this->assertEquals(Http::STATUS_UNPROCESSABLE_ENTITY, $response->getStatus());

		$response = $this->controller->update(
			1,
			'mount',
			'\OC\Files\Storage\InvalidStorage',
			array(),
			[],
			[],
			[],
			null
		);

		$this->assertEquals(Http::STATUS_UNPROCESSABLE_ENTITY, $response->getStatus());
	}

	public function testUpdateStorageNonExisting() {
		$this->service->expects($this->once())
			->method('updateStorage')
			->will($this->throwException(new NotFoundException()));

		$response = $this->controller->update(
			255,
			'mount',
			'\OC\Files\Storage\SMB',
			array(),
			[],
			[],
			[],
			null
		);

		$this->assertEquals(Http::STATUS_NOT_FOUND, $response->getStatus());
	}

	public function testDeleteStorage() {
		$this->service->expects($this->once())
			->method('removeStorage');

		$response = $this->controller->destroy(1);
		$this->assertEquals(Http::STATUS_NO_CONTENT, $response->getStatus());
	}

	public function testDeleteStorageNonExisting() {
		$this->service->expects($this->once())
			->method('removeStorage')
			->will($this->throwException(new NotFoundException()));

		$response = $this->controller->destroy(255);
		$this->assertEquals(Http::STATUS_NOT_FOUND, $response->getStatus());
	}

	public function testGetStorage() {
		$storageConfig = new StorageConfig(1);
		$storageConfig->setMountPoint('test');
		$storageConfig->setBackendClass('\OC\Files\Storage\SMB');
		$storageConfig->setBackendOptions(['user' => 'test', 'password', 'password123']);
		$storageConfig->setMountOptions(['priority' => false]);

		$this->service->expects($this->once())
			->method('getStorage')
			->with(1)
			->will($this->returnValue($storageConfig));
		$response = $this->controller->show(1);

		$this->assertEquals(Http::STATUS_OK, $response->getStatus());
		$this->assertEquals($storageConfig, $response->getData());
	}
}
