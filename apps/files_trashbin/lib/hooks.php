<?php

/**
 * This class contains all hooks.
 */

namespace OCA\Files_Trashbin;

class Hooks {

	/**
	 * clean up user specific settings if user gets deleted
	 * @param array $params array with uid
	 *
	 * This function is connected to the pre_deleteUser signal of OC_Users
	 * to remove the used space for the trash bin stored in the database
	 */
	public static function deleteUser_hook($params) {
		if( \OCP\App::isEnabled('files_trashbin') ) {
			$uid = $params['uid'];
			Trashbin::deleteUser($uid);
			}
	}

	public static function post_write_hook($params) {
		Trashbin::resizeTrash(\OCP\User::getUser());
	}
}
