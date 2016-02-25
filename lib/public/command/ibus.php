<?php

namespace OCP\Command;

/**
 * Interface IBus
 *
 * @package OCP\Command
 * @since 8.1.0
 */
interface IBus {
	/**
	 * Schedule a command to be fired
	 *
	 * @param \OCP\Command\ICommand | callable $command
	 * @since 8.1.0
	 */
	public function push($command);

	/**
	 * Require all commands using a trait to be run synchronous
	 *
	 * @param string $trait
	 * @since 8.1.0
	 */
	public function requireSync($trait);
}
