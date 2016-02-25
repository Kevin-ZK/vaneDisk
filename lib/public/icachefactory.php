<?php

namespace OCP;

/**
 * Interface ICacheFactory
 *
 * @package OCP
 * @since 7.0.0
 */
interface ICacheFactory{
	/**
	 * Get a memory cache instance
	 *
	 * All entries added trough the cache instance will be namespaced by $prefix to prevent collisions between apps
	 *
	 * @param string $prefix
	 * @return \OCP\ICache
	 * @since 7.0.0
	 */
	public function create($prefix = '');

	/**
	 * Check if any memory cache backend is available
	 *
	 * @return bool
	 * @since 7.0.0
	 */
	public function isAvailable();
}
