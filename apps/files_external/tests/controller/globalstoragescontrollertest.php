<?php
namespace OCA\Files_external\Tests\Controller;

use \OCA\Files_external\Controller\GlobalStoragesController;
use \OCA\Files_external\Service\GlobalStoragesService;
use \OCP\AppFramework\Http;
use \OCA\Files_external\NotFoundException;

class GlobalStoragesControllerTest extends StoragesControllerTest {
	public function setUp() {
		parent::setUp();
		$this->service = $this->getMock('\OCA\Files_external\Service\GlobalStoragesService');

		$this->controller = new GlobalStoragesController(
			'files_external',
			$this->getMock('\OCP\IRequest'),
			$this->getMock('\OCP\IL10N'),
			$this->service
		);
	}
}
