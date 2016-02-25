<?php

namespace OC\Hooks;

/**
 * Class ForwardingEmitter
 *
 * allows forwarding all listen calls to other emitters
 *
 * @package OC\Hooks
 */
abstract class ForwardingEmitter extends BasicEmitter {
	/**
	 * @var \OC\Hooks\Emitter[] array
	 */
	private $forwardEmitters = array();

	/**
	 * @param string $scope
	 * @param string $method
	 * @param callable $callback
	 */
	public function listen($scope, $method, callable $callback) {
		parent::listen($scope, $method, $callback);
		foreach ($this->forwardEmitters as $emitter) {
			$emitter->listen($scope, $method, $callback);
		}
	}

	/**
	 * @param \OC\Hooks\Emitter $emitter
	 */
	protected function forward(Emitter $emitter) {
		$this->forwardEmitters[] = $emitter;

		//forward all previously connected hooks
		foreach ($this->listeners as $key => $listeners) {
			list($scope, $method) = explode('::', $key, 2);
			foreach ($listeners as $listener) {
				$emitter->listen($scope, $method, $listener);
			}
		}
	}
}
