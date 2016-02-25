<?php
namespace OCP;

/**
 * Class to generate URLs
 * @since 6.0.0
 */
interface IURLGenerator {
	/**
	 * Returns the URL for a route
	 * @param string $routeName the name of the route
	 * @param array $arguments an array with arguments which will be filled into the url
	 * @return string the url
	 * @since 6.0.0
	 */
	public function linkToRoute($routeName, $arguments = array());

	/**
	 * Returns the absolute URL for a route
	 * @param string $routeName the name of the route
	 * @param array $arguments an array with arguments which will be filled into the url
	 * @return string the absolute url
	 * @since 8.0.0
	 */
	public function linkToRouteAbsolute($routeName, $arguments = array());

	/**
	 * Returns an URL for an image or file
	 * @param string $appName the name of the app
	 * @param string $file the name of the file
	 * @param array $args array with param=>value, will be appended to the returned url
	 *    The value of $args will be urlencoded
	 * @return string the url
	 * @since 6.0.0
	 */
	public function linkTo($appName, $file, $args = array());

	/**
	 * Returns the link to an image, like linkTo but only with prepending img/
	 * @param string $appName the name of the app
	 * @param string $file the name of the file
	 * @return string the url
	 * @since 6.0.0
	 */
	public function imagePath($appName, $file);


	/**
	 * Makes an URL absolute
	 * @param string $url the url in the vanedisk host
	 * @return string the absolute version of the url
	 * @since 6.0.0
	 */
	public function getAbsoluteURL($url);

	/**
	 * @param string $key
	 * @return string url to the online documentation
	 * @since 8.0.0
	 */
	public function linkToDocs($key);
}
