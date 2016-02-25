<?php

namespace OC\AppFramework\routing;

use \OC\AppFramework\App;
use \OC\AppFramework\DependencyInjection\DIContainer;

class RouteActionHandler {
	private $controllerName;
	private $actionName;
	private $container;

	/**
	 * @param string $controllerName
	 * @param string $actionName
	 */
	public function __construct(DIContainer $container, $controllerName, $actionName) {
		$this->controllerName = $controllerName;
		$this->actionName = $actionName;
		$this->container = $container;
	}

	public function __invoke($params) {
		App::main($this->controllerName, $this->actionName, $this->container, $params);
	}
}
