<?php

namespace OC\Command;

use OC\BackgroundJob\QueuedJob;

class CallableJob extends QueuedJob {
	protected function run($serializedCallable) {
		$callable = unserialize($serializedCallable);
		if (is_callable($callable)) {
			$callable();
		} else {
			throw new \InvalidArgumentException('Invalid serialized callable');
		}
	}
}
