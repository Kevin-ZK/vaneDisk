<?php

namespace OC\Command;

use OC\BackgroundJob\QueuedJob;
use OCP\Command\ICommand;

/**
 * Wrap a command in the background job interface
 */
class CommandJob extends QueuedJob {
	protected function run($serializedCommand) {
		$command = unserialize($serializedCommand);
		if ($command instanceof ICommand) {
			$command->handle();
		} else {
			throw new \InvalidArgumentException('Invalid serialized command');
		}
	}
}
