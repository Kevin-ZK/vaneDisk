<?php

namespace OCA\Files\External;

class Api {

	/**
	 * Formats the given mount config to a mount entry.
	 * 
	 * @param string $mountPoint mount point name, relative to the data dir
	 * @param array $mountConfig mount config to format
	 *
	 * @return array entry
	 */
	private static function formatMount($mountPoint, $mountConfig) {
		// strip "/$user/files" from mount point
		$mountPoint = explode('/', trim($mountPoint, '/'), 3);
		$mountPoint = $mountPoint[2];

		// split path from mount point
		$path = dirname($mountPoint);
		if ($path === '.') {
			$path = '';
		}

		$isSystemMount = !$mountConfig['personal'];

		$permissions = \OCP\Constants::PERMISSION_READ;
		// personal mounts can be deleted
		if (!$isSystemMount) {
			$permissions |= \OCP\Constants::PERMISSION_DELETE;
		}

		$entry = array(
			'name' => basename($mountPoint),
			'path' => $path,
			'type' => 'dir',
			'backend' => $mountConfig['backend'],
			'scope' => ( $isSystemMount ? 'system' : 'personal' ),
			'permissions' => $permissions
		);
		return $entry;
	}

	/**
	 * Returns the mount points visible for this user.
	 *
	 * @param array $params
	 * @return \OC_OCS_Result share information
	 */
	public static function getUserMounts($params) {
		$entries = array();
		$user = \OC_User::getUser();

		$mounts = \OC_Mount_Config::getAbsoluteMountPoints($user);
		foreach($mounts as $mountPoint => $mount) {
			$entries[] = self::formatMount($mountPoint, $mount);
		}

		return new \OC_OCS_Result($entries);
	}
}
