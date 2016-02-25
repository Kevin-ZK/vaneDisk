<?php

namespace OCP\Route;

/**
 * Interface IRouter
 *
 * @package OCP\Route
 * @since 7.0.0
 */
interface IRouter {

	/**
	 * Get the files to load the routes from
	 *
	 * @return string[]
	 * @since 7.0.0
	 */
	public function getRoutingFiles();

	/**
	 * @return string
	 * @since 7.0.0
	 */
	public function getCacheKey();

	/**
	 * loads the api routes
	 * @return void
	 * @since 7.0.0
	 */
	public function loadRoutes($app = null);

	/**
	 * Sets the collection to use for adding routes
	 *
	 * @param string $name Name of the collection to use.
	 * @return void
	 * @since 7.0.0
	 */
	public function useCollection($name);

	/**
	 * returns the current collection name in use for adding routes
	 *
	 * @return string the collection name
	 * @since 8.0.0
	 */
	public function getCurrentCollection();

	/**
	 * Create a \OCP\Route\IRoute.
	 *
	 * @param string $name Name of the route to create.
	 * @param string $pattern The pattern to match
	 * @param array $defaults An array of default parameter values
	 * @param array $requirements An array of requirements for parameters (regexes)
	 * @return \OCP\Route\IRoute
	 * @since 7.0.0
	 */
	public function create($name, $pattern, array $defaults = array(), array $requirements = array());

	/**
	 * Find the route matching $url.
	 *
	 * @param string $url The url to find
	 * @throws \Exception
	 * @return void
	 * @since 7.0.0
	 */
	public function match($url);

	/**
	 * Get the url generator
	 *
	 * @since 7.0.0
	 */
	public function getGenerator();

	/**
	 * Generate url based on $name and $parameters
	 *
	 * @param string $name Name of the route to use.
	 * @param array $parameters Parameters for the route
	 * @param bool $absolute
	 * @return string
	 * @since 7.0.0
	 */
	public function generate($name, $parameters = array(), $absolute = false);

}
