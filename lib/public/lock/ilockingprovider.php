<?php

namespace OCP\Lock;

/**
 * Interface ILockingProvider
 *
 * @package OCP\Lock
 * @since 8.1.0
 */
interface ILockingProvider {
	/**
	 * @since 8.1.0
	 */
	const LOCK_SHARED = 1;
	/**
	 * @since 8.1.0
	 */
	const LOCK_EXCLUSIVE = 2;

	/**
	 * @param string $path
	 * @param int $type self::LOCK_SHARED or self::LOCK_EXCLUSIVE
	 * @return bool
	 * @since 8.1.0
	 */
	public function isLocked($path, $type);

	/**
	 * @param string $path
	 * @param int $type self::LOCK_SHARED or self::LOCK_EXCLUSIVE
	 * @throws \OCP\Lock\LockedException
	 * @since 8.1.0
	 */
	public function acquireLock($path, $type);

	/**
	 * @param string $path
	 * @param int $type self::LOCK_SHARED or self::LOCK_EXCLUSIVE
	 * @since 8.1.0
	 */
	public function releaseLock($path, $type);

	/**
	 * Change the type of an existing lock
	 *
	 * @param string $path
	 * @param int $targetType self::LOCK_SHARED or self::LOCK_EXCLUSIVE
	 * @throws \OCP\Lock\LockedException
	 * @since 8.1.0
	 */
	public function changeLock($path, $targetType);

	/**
	 * release all lock acquired by this instance
	 * @since 8.1.0
	 */
	public function releaseAll();
}
