<?php

namespace OCA\Files_External\Appinfo;

use \OCA\Files_External\Controller\AjaxController;
use \OCP\AppFramework\App;
use \OCP\IContainer;

/**
 * @package OCA\Files_External\Appinfo
 */
class Application extends App {
	public function __construct(array $urlParams=array()) {
		parent::__construct('files_external', $urlParams);

		$container = $this->getContainer();

		/**
		 * Controllers
		 */
		$container->registerService('AjaxController', function (IContainer $c) {
			return new AjaxController(
				$c->query('AppName'),
				$c->query('Request')
			);
		});
	}
}
