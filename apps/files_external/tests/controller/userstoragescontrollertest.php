<?php
namespace OCA\Files_external\Tests\Controller;

use \OCA\Files_external\Controller\UserStoragesController;
use \OCA\Files_external\Service\UserStoragesService;
use \OCP\AppFramework\Http;
use \OCA\Files_external\NotFoundException;

class UserStoragesControllerTest extends StoragesControllerTest {

	/**
	 * @var array
	 */
	private $oldAllowedBackends;

	public function setUp() {
		parent::setUp();
		$this->service = $this->getMockBuilder('\OCA\Files_external\Service\UserStoragesService')
			->disableOriginalConstructor()
			->getMock();

		$this->controller = new UserStoragesController(
			'files_external',
			$this->getMock('\OCP\IRequest'),
			$this->getMock('\OCP\IL10N'),
			$this->service
		);

		$config = \OC::$server->getConfig();

		$this->oldAllowedBackends = $config->getAppValue(
			'files_external',
			'user_mounting_backends',
			''
		);
		$config->setAppValue(
			'files_external',
			'user_mounting_backends',
			'\OC\Files\Storage\SMB'
		);
	}

	public function tearDown() {
		$config = \OC::$server->getConfig();
		$config->setAppValue(
			'files_external',
			'user_mounting_backends',
			$this->oldAllowedBackends
		);
		parent::tearDown();
	}

	function disallowedBackendClassProvider() {
		return array(
			array('\OC\Files\Storage\Local'),
			array('\OC\Files\Storage\FTP'),
		);
	}
	/**
	 * @dataProvider disallowedBackendClassProvider
	 */
	public function testAddOrUpdateStorageDisallowedBackend($backendClass) {
		$this->service->expects($this->never())
			->method('addStorage');
		$this->service->expects($this->never())
			->method('updateStorage');

		$response = $this->controller->create(
			'mount',
			$backendClass,
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
			$backendClass,
			array(),
			[],
			[],
			[],
			null
		);

		$this->assertEquals(Http::STATUS_UNPROCESSABLE_ENTITY, $response->getStatus());
	}

}
