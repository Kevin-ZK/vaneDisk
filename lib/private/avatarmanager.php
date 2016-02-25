<?php

namespace OC;

use OCP\IAvatarManager;
use OC\Avatar;

/**
 * This class implements methods to access Avatar functionality
 */
class AvatarManager implements IAvatarManager {

	/**
	 * return a user specific instance of \OCP\IAvatar
	 * @see \OCP\IAvatar
	 * @param string $user the ownCloud user id
	 * @return \OCP\IAvatar
	 * @throws \Exception In case the username is potentially dangerous
	 */
	public function getAvatar($user) {
		return new Avatar($user);
	}
}
