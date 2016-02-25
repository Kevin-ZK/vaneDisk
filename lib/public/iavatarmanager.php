<?php

namespace OCP;

/**
 * This class provides avatar functionality
 * @since 6.0.0
 */

interface IAvatarManager {

	/**
	 * return a user specific instance of \OCP\IAvatar
	 * @see \OCP\IAvatar
	 * @param string $user the ownCloud user id
	 * @return \OCP\IAvatar
	 * @since 6.0.0
	 */
	public function getAvatar($user);
}
