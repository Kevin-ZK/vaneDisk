<?php

namespace OCP\Files\Config;

use OCP\Files\Storage\IStorageFactory;
use OCP\IUser;

/**
 * Provides
 * @since 8.0.0
 */
interface IMountProvider {
	/**
	 * Get all mountpoints applicable for the user
	 *
	 * @param \OCP\IUser $user
	 * @param \OCP\Files\Storage\IStorageFactory $loader
	 * @return \OCP\Files\Mount\IMountPoint[]
	 * @since 8.0.0
	 */
	public function getMountsForUser(IUser $user, IStorageFactory $loader);
}
