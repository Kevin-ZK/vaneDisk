<?php
namespace OCP;

/**
 * Interface ISession
 *
 * wrap PHP's internal session handling into the ISession interface
 * @since 6.0.0
 */
interface ISession {

	/**
	 * Set a value in the session
	 *
	 * @param string $key
	 * @param mixed $value
	 * @since 6.0.0
	 */
	public function set($key, $value);

	/**
	 * Get a value from the session
	 *
	 * @param string $key
	 * @return mixed should return null if $key does not exist
	 * @since 6.0.0
	 */
	public function get($key);

	/**
	 * Check if a named key exists in the session
	 *
	 * @param string $key
	 * @return bool
	 * @since 6.0.0
	 */
	public function exists($key);

	/**
	 * Remove a $key/$value pair from the session
	 *
	 * @param string $key
	 * @since 6.0.0
	 */
	public function remove($key);

	/**
	 * Reset and recreate the session
	 * @since 6.0.0
	 */
	public function clear();

	/**
	 * Close the session and release the lock
	 * @since 7.0.0
	 */
	public function close();

}
