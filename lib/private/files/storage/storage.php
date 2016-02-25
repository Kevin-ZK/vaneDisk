<?php

namespace OC\Files\Storage;
use OCP\Lock\ILockingProvider;

/**
 * Provide a common interface to all different storage options
 *
 * All paths passed to the storage are relative to the storage and should NOT have a leading slash.
 */
interface Storage extends \OCP\Files\Storage {

	/**
	 * get a cache instance for the storage
	 *
	 * @param string $path
	 * @param \OC\Files\Storage\Storage (optional) the storage to pass to the cache
	 * @return \OC\Files\Cache\Cache
	 */
	public function getCache($path = '', $storage = null);

	/**
	 * get a scanner instance for the storage
	 *
	 * @param string $path
	 * @param \OC\Files\Storage\Storage (optional) the storage to pass to the scanner
	 * @return \OC\Files\Cache\Scanner
	 */
	public function getScanner($path = '', $storage = null);


	/**
	 * get the user id of the owner of a file or folder
	 *
	 * @param string $path
	 * @return string
	 */
	public function getOwner($path);

	/**
	 * get a watcher instance for the cache
	 *
	 * @param string $path
	 * @param \OC\Files\Storage\Storage (optional) the storage to pass to the watcher
	 * @return \OC\Files\Cache\Watcher
	 */
	public function getWatcher($path = '', $storage = null);

	/**
	 * @return \OC\Files\Cache\Storage
	 */
	public function getStorageCache();

	/**
	 * @param string $path
	 * @return array
	 */
	public function getMetaData($path);

	/**
	 * @param string $path The path of the file to acquire the lock for
	 * @param int $type \OCP\Lock\ILockingProvider::LOCK_SHARED or \OCP\Lock\ILockingProvider::LOCK_EXCLUSIVE
	 * @param \OCP\Lock\ILockingProvider $provider
	 * @throws \OCP\Lock\LockedException
	 */
	public function acquireLock($path, $type, ILockingProvider $provider);

	/**
	 * @param string $path The path of the file to release the lock for
	 * @param int $type \OCP\Lock\ILockingProvider::LOCK_SHARED or \OCP\Lock\ILockingProvider::LOCK_EXCLUSIVE
	 * @param \OCP\Lock\ILockingProvider $provider
	 */
	public function releaseLock($path, $type, ILockingProvider $provider);

	/**
	 * @param string $path The path of the file to change the lock for
	 * @param int $type \OCP\Lock\ILockingProvider::LOCK_SHARED or \OCP\Lock\ILockingProvider::LOCK_EXCLUSIVE
	 * @param \OCP\Lock\ILockingProvider $provider
	 * @throws \OCP\Lock\LockedException
	 */
	public function changeLock($path, $type, ILockingProvider $provider);
}
