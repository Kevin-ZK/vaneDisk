<?php

namespace OCP;


/**
 * Small Interface for Search
 * @since 7.0.0
 */
interface ISearch {

	/**
	 * Search all providers for $query
	 * @param string $query
	 * @param string[] $inApps optionally limit results to the given apps
	 * @return array An array of OCP\Search\Result's
	 * @deprecated 8.0.0 use searchPaged() with page and size
	 * @since 7.0.0 - parameter $inApps was added in 8.0.0
	 */
	public function search($query, array $inApps = array());

	/**
	 * Search all providers for $query
	 * @param string $query
	 * @param string[] $inApps optionally limit results to the given apps
	 * @param int $page pages start at page 1
	 * @param int $size
	 * @return array An array of OCP\Search\Result's
	 * @since 8.0.0
	 */
	public function searchPaged($query, array $inApps = array(), $page = 1, $size = 30);

	/**
	 * Register a new search provider to search with
	 * @param string $class class name of a OCP\Search\Provider
	 * @param array $options optional
	 * @since 7.0.0
	 */
	public function registerProvider($class, array $options = array());

	/**
	 * Remove one existing search provider
	 * @param string $provider class name of a OCP\Search\Provider
	 * @since 7.0.0
	 */
	public function removeProvider($provider);

	/**
	 * Remove all registered search providers
	 * @since 7.0.0
	 */
	public function clearProviders();

}
