<?php

namespace OCP\Files\Config;

use OCP\IUser;

/**
 * Manages the different mount providers
 * @since 8.0.0
 */
interface IMountProviderCollection {
	/**
	 * Get all configured mount points for the user
	 *
	 * @param \OCP\IUser $user
	 * @return \OCP\Files\Mount\IMountPoint[]
	 * @since 8.0.0
	 */
	public function getMountsForUser(IUser $user);

	/**
	 * Add a provider for mount points
	 *
	 * @param \OCP\Files\Config\IMountProvider $provider
	 * @since 8.0.0
	 */
	public function registerProvider(IMountProvider $provider);
}
