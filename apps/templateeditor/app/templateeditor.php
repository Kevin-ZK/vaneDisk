<?php

namespace OCA\TemplateEditor\App;

use OCP\AppFramework\App;
use OCA\TemplateEditor\Controller\AdminSettingsController;

class TemplateEditor extends App {

	public function __construct(array $urlParams=array()){
		parent::__construct('templateeditor', $urlParams);

		$container = $this->getContainer();

		/**
		 * Controllers
		 */
		$container->registerService('AdminSettingsController', function($c) {
			return new AdminSettingsController(
				$c->query('AppName'),
				$c->query('Request')
			);
		});
	}
}
