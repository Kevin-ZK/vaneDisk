<?php
namespace OCP;

/**
 * This interface defines method for accessing the file based user cache.
 *
 * @since 8.1.0
 */
interface IMemcache extends ICache {
	/**
	 * Set a value in the cache if it's not already stored
	 *
	 * @param string $key
	 * @param mixed $value
	 * @param int $ttl Time To Live in seconds. Defaults to 60*60*24
	 * @return bool
	 * @since 8.1.0
	 */
	public function add($key, $value, $ttl = 0);

	/**
	 * Increase a stored number
	 *
	 * @param string $key
	 * @param int $step
	 * @return int | bool
	 * @since 8.1.0
	 */
	public function inc($key, $step = 1);

	/**
	 * Decrease a stored number
	 *
	 * @param string $key
	 * @param int $step
	 * @return int | bool
	 * @since 8.1.0
	 */
	public function dec($key, $step = 1);

	/**
	 * Compare and set
	 *
	 * @param string $key
	 * @param mixed $old
	 * @param mixed $new
	 * @return bool
	 * @since 8.1.0
	 */
	public function cas($key, $old, $new);

	/**
	 * Compare and delete
	 *
	 * @param string $key
	 * @param mixed $old
	 * @return bool
	 * @since 8.1.0
	 */
	public function cad($key, $old);
}
