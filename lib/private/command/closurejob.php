<?php

namespace OC\Command;

use OC\BackgroundJob\QueuedJob;
use SuperClosure\Serializer;

class ClosureJob extends QueuedJob {
	protected function run($serializedCallable) {
		$serializer = new Serializer();
		$callable = $serializer->unserialize($serializedCallable);
		if (is_callable($callable)) {
			$callable();
		} else {
			throw new \InvalidArgumentException('Invalid serialized callable');
		}
	}
}
