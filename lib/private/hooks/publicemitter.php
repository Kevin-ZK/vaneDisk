<?php

namespace OC\Hooks;

class PublicEmitter extends BasicEmitter {
	/**
	 * @param string $scope
	 * @param string $method
	 * @param array $arguments optional
	 */
	public function emit($scope, $method, array $arguments = array()) {
		parent::emit($scope, $method, $arguments);
	}
}
