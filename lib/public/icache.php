<?php
namespace OCP;

/**
 * This interface defines method for accessing the file based user cache.
 * @since 6.0.0
 */
interface ICache {

	/**
	 * Get a value from the user cache
	 * @param string $key
	 * @return mixed
	 * @since 6.0.0
	 */
	public function get($key);

	/**
	 * Set a value in the user cache
	 * @param string $key
	 * @param mixed $value
	 * @param int $ttl Time To Live in seconds. Defaults to 60*60*24
	 * @return bool
	 * @since 6.0.0
	 */
	public function set($key, $value, $ttl = 0);

	/**
	 * Check if a value is set in the user cache
	 * @param string $key
	 * @return bool
	 * @since 6.0.0
	 */
	public function hasKey($key);

	/**
	 * Remove an item from the user cache
	 * @param string $key
	 * @return bool
	 * @since 6.0.0
	 */
	public function remove($key);

	/**
	 * Clear the user cache of all entries starting with a prefix
	 * @param string $prefix (optional)
	 * @return bool
	 * @since 6.0.0
	 */
	public function clear($prefix = '');
}
