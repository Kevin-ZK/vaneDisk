<?php

namespace OC\Hooks;

abstract class LegacyEmitter extends BasicEmitter {
	protected function emit($scope, $method, array $arguments = array()) {
		\OC_Hook::emit($scope, $method, $arguments);
		parent::emit($scope, $method, $arguments);
	}
}
