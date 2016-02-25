<?php

namespace OC\BackgroundJob\Legacy;

class QueuedJob extends \OC\BackgroundJob\QueuedJob {
	public function run($argument) {
		$class = $argument['klass'];
		$method = $argument['method'];
		$parameters = $argument['parameters'];
		if (is_callable(array($class, $method))) {
			call_user_func(array($class, $method), $parameters);
		}
	}
}
