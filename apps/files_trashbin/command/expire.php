<?php

namespace OCA\Files_Trashbin\Command;

use OC\Command\FileAccess;
use OCA\Files_Trashbin\Trashbin;
use OCP\Command\ICommand;

class Expire implements ICommand {
	use FileAccess;

	/**
	 * @var string
	 */
	private $user;

	/**
	 * @var int
	 */
	private $trashBinSize;

	/**
	 * @param string $user
	 * @param int $trashBinSize
	 */
	function __construct($user, $trashBinSize) {
		$this->user = $user;
		$this->trashBinSize = $trashBinSize;
	}

	public function handle() {
		$userManager = \OC::$server->getUserManager();
		if (!$userManager->userExists($this->user)) {
			// User has been deleted already
			return;
		}

		\OC_Util::tearDownFS();
		\OC_Util::setupFS($this->user);
		Trashbin::expire($this->trashBinSize, $this->user);
		\OC_Util::tearDownFS();
	}
}
