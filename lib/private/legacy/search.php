<?php

/**
 * provides an interface to all search providers
 *
 * @deprecated use \OCP\ISearch / \OC\Search instead
 */
class OC_Search {
	/**
	 * @return \OCP\ISearch
	 */
	private static function getSearch() {
		return \OC::$server->getSearch();
	}

	/**
	 * Search all providers for $query
	 * @param string $query
	 * @return array An array of OCP\Search\Result's
	 */
	public static function search($query) {
		return self::getSearch()->search($query);
	}

	/**
	 * Register a new search provider to search with
	 * @param string $class class name of a OCP\Search\Provider
	 * @param array $options optional
	 */
	public static function registerProvider($class, $options = array()) {
		return self::getSearch()->registerProvider($class, $options);
	}

	/**
	 * Remove one existing search provider
	 * @param string $provider class name of a OCP\Search\Provider
	 */
	public static function removeProvider($provider) {
		return self::getSearch()->removeProvider($provider);
	}

	/**
	 * Remove all registered search providers
	 */
	public static function clearProviders() {
		return self::getSearch()->clearProviders();
	}

}
