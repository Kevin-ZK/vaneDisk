<?php

namespace OC\Encryption;

class File implements \OCP\Encryption\IFile {

	/** @var Util */
	protected $util;

	public function __construct(Util $util) {
		$this->util = $util;
	}


	/**
	 * get list of users with access to the file
	 *
	 * @param string $path to the file
	 * @return array  ['users' => $uniqueUserIds, 'public' => $public]
	 */
	public function getAccessList($path) {

		// Make sure that a share key is generated for the owner too
		list($owner, $ownerPath) = $this->util->getUidAndFilename($path);

		// always add owner to the list of users with access to the file
		$userIds = array($owner);

		if (!$this->util->isFile($owner . '/' . $ownerPath)) {
			return array('users' => $userIds, 'public' => false);
		}

		$ownerPath = substr($ownerPath, strlen('/files'));
		$ownerPath = $this->util->stripPartialFileExtension($ownerPath);

		// Find out who, if anyone, is sharing the file
		$result = \OCP\Share::getUsersSharingFile($ownerPath, $owner);
		$userIds = \array_merge($userIds, $result['users']);
		$public = $result['public'] || $result['remote'];

		// check if it is a group mount
		if (\OCP\App::isEnabled("files_external")) {
			$mounts = \OC_Mount_Config::getSystemMountPoints();
			foreach ($mounts as $mount) {
				if ($mount['mountpoint'] == substr($ownerPath, 1, strlen($mount['mountpoint']))) {
					$mountedFor = $this->util->getUserWithAccessToMountPoint($mount['applicable']['users'], $mount['applicable']['groups']);
					$userIds = array_merge($userIds, $mountedFor);
				}
			}
		}

		// Remove duplicate UIDs
		$uniqueUserIds = array_unique($userIds);

		return array('users' => $uniqueUserIds, 'public' => $public);
	}

}
