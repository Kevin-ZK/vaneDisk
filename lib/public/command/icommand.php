<?php

namespace OCP\Command;

/**
 * Interface ICommand
 *
 * @package OCP\Command
 * @since 8.1.0
 */
interface ICommand {
	/**
	 * Run the command
	 * @since 8.1.0
	 */
	public function handle();
}
