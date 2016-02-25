<?php

namespace OC\Encryption;

use OC\Files\Filesystem;
use \OC\Files\Mount;
use \OC\Files\View;

/**
 * update encrypted files, e.g. because a file was shared
 */
class Update {

	/** @var \OC\Files\View */
	protected $view;

	/** @var \OC\Encryption\Util */
	protected $util;

	 /** @var \OC\Files\Mount\Manager */
	protected $mountManager;

	/** @var \OC\Encryption\Manager */
	protected $encryptionManager;

	/** @var string */
	protected $uid;

	/** @var \OC\Encryption\File */
	protected $file;

	/**
	 *
	 * @param \OC\Files\View $view
	 * @param \OC\Encryption\Util $util
	 * @param \OC\Files\Mount\Manager $mountManager
	 * @param \OC\Encryption\Manager $encryptionManager
	 * @param \OC\Encryption\File $file
	 * @param string $uid
	 */
	public function __construct(
			View $view,
			Util $util,
			Mount\Manager $mountManager,
			Manager $encryptionManager,
			File $file,
			$uid
		) {

		$this->view = $view;
		$this->util = $util;
		$this->mountManager = $mountManager;
		$this->encryptionManager = $encryptionManager;
		$this->file = $file;
		$this->uid = $uid;
	}

	/**
	 * hook after file was shared
	 *
	 * @param array $params
	 */
	public function postShared($params) {
		if ($this->encryptionManager->isEnabled()) {
			if ($params['itemType'] === 'file' || $params['itemType'] === 'folder') {
				$path = Filesystem::getPath($params['fileSource']);
				list($owner, $ownerPath) = $this->getOwnerPath($path);
				$absPath = '/' . $owner . '/files/' . $ownerPath;
				$this->update($absPath);
			}
		}
	}

	/**
	 * hook after file was unshared
	 *
	 * @param array $params
	 */
	public function postUnshared($params) {
		if ($this->encryptionManager->isEnabled()) {
			if ($params['itemType'] === 'file' || $params['itemType'] === 'folder') {
				$path = Filesystem::getPath($params['fileSource']);
				list($owner, $ownerPath) = $this->getOwnerPath($path);
				$absPath = '/' . $owner . '/files/' . $ownerPath;
				$this->update($absPath);
			}
		}
	}

	/**
	 * inform encryption module that a file was restored from the trash bin,
	 * e.g. to update the encryption keys
	 *
	 * @param array $params
	 */
	public function postRestore($params) {
		if ($this->encryptionManager->isEnabled()) {
			$path = Filesystem::normalizePath('/' . $this->uid . '/files/' . $params['filePath']);
			$this->update($path);
		}
	}

	/**
	 * inform encryption module that a file was renamed,
	 * e.g. to update the encryption keys
	 *
	 * @param array $params
	 */
	public function postRename($params) {
		$source = $params['oldpath'];
		$target = $params['newpath'];
		if(
			$this->encryptionManager->isEnabled() &&
			dirname($source) !== dirname($target)
		) {
				list($owner, $ownerPath) = $this->getOwnerPath($target);
				$absPath = '/' . $owner . '/files/' . $ownerPath;
				$this->update($absPath);
		}
	}

	/**
	 * get owner and path relative to data/<owner>/files
	 *
	 * @param string $path path to file for current user
	 * @return array ['owner' => $owner, 'path' => $path]
	 * @throw \InvalidArgumentException
	 */
	protected function getOwnerPath($path) {
		$info = Filesystem::getFileInfo($path);
		$owner = Filesystem::getOwner($path);
		$view = new View('/' . $owner . '/files');
		$path = $view->getPath($info->getId());
		if ($path === null) {
			throw new \InvalidArgumentException('No file found for ' . $info->getId());
		}

		return array($owner, $path);
	}

	/**
	 * notify encryption module about added/removed users from a file/folder
	 *
	 * @param string $path relative to data/
	 * @throws Exceptions\ModuleDoesNotExistsException
	 */
	public function update($path) {

		// if a folder was shared, get a list of all (sub-)folders
		if ($this->view->is_dir($path)) {
			$allFiles = $this->util->getAllFiles($path);
		} else {
			$allFiles = array($path);
		}

		$encryptionModule = $this->encryptionManager->getEncryptionModule();

		foreach ($allFiles as $file) {
			$usersSharing = $this->file->getAccessList($file);
			$encryptionModule->update($file, $this->uid, $usersSharing);
		}
	}

}
