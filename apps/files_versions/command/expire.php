<?php

namespace OCA\Files_Versions\Command;

use OC\Command\FileAccess;
use OCA\Files_Versions\Storage;
use OCP\Command\ICommand;

class Expire implements ICommand {
	use FileAccess;

	/**
	 * @var string
	 */
	private $fileName;

	/**
	 * @var int|null
	 */
	private $versionsSize;

	/**
	 * @var int
	 */
	private $neededSpace = 0;

	/**
	 * @var string
	 */
	private $user;

	/**
	 * @param string $user
	 * @param string $fileName
	 * @param int|null $versionsSize
	 * @param int $neededSpace
	 */
	function __construct($user, $fileName, $versionsSize = null, $neededSpace = 0) {
		$this->user = $user;
		$this->fileName = $fileName;
		$this->versionsSize = $versionsSize;
		$this->neededSpace = $neededSpace;
	}


	public function handle() {
		$userManager = \OC::$server->getUserManager();
		if (!$userManager->userExists($this->user)) {
			// User has been deleted already
			return;
		}

		\OC_Util::setupFS($this->user);
		Storage::expire($this->fileName, $this->versionsSize, $this->neededSpace);
		\OC_Util::tearDownFS();
	}
}
