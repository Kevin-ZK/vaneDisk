<?php

namespace OCP\Files\Mount;

/**
 * A storage mounted to folder on the filesystem
 * @since 8.0.0
 */
interface IMountPoint {

	/**
	 * get complete path to the mount point
	 *
	 * @return string
	 * @since 8.0.0
	 */
	public function getMountPoint();

	/**
	 * Set the mountpoint
	 *
	 * @param string $mountPoint new mount point
	 * @since 8.0.0
	 */
	public function setMountPoint($mountPoint);

	/**
	 * Get the storage that is mounted
	 *
	 * @return \OC\Files\Storage\Storage
	 * @since 8.0.0
	 */
	public function getStorage();

	/**
	 * Get the id of the storages
	 *
	 * @return string
	 * @since 8.0.0
	 */
	public function getStorageId();

	/**
	 * Get the path relative to the mountpoint
	 *
	 * @param string $path absolute path to a file or folder
	 * @return string
	 * @since 8.0.0
	 */
	public function getInternalPath($path);

	/**
	 * Apply a storage wrapper to the mounted storage
	 *
	 * @param callable $wrapper
	 * @since 8.0.0
	 */
	public function wrapStorage($wrapper);

	/**
	 * Get a mount option
	 *
	 * @param string $name Name of the mount option to get
	 * @param mixed $default Default value for the mount option
	 * @return mixed
	 * @since 8.0.0
	 */
	public function getOption($name, $default);

	/**
	 * Get all options for the mount
	 *
	 * @return array
	 * @since 8.1.0
	 */
	public function getOptions();
}
