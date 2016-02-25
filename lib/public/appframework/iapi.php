<?php

namespace OCP\AppFramework;


/**
 * A few very basic and frequently used API functions are combined in here
 * @deprecated 8.0.0
 */
interface IApi {


	/**
	 * Gets the userid of the current user
	 * @return string the user id of the current user
	 * @deprecated 8.0.0 Use \OC::$server->getUserSession()->getUser()->getUID()
	 */
	function getUserId();


	/**
	 * Adds a new javascript file
	 * @deprecated 8.0.0 include javascript and css in template files
	 * @param string $scriptName the name of the javascript in js/ without the suffix
	 * @param string $appName the name of the app, defaults to the current one
	 * @return void
	 */
	function addScript($scriptName, $appName = null);


	/**
	 * Adds a new css file
	 * @deprecated 8.0.0 include javascript and css in template files
	 * @param string $styleName the name of the css file in css/without the suffix
	 * @param string $appName the name of the app, defaults to the current one
	 * @return void
	 */
	function addStyle($styleName, $appName = null);


	/**
	 * @deprecated 8.0.0 include javascript and css in template files
	 * shorthand for addScript for files in the 3rdparty directory
	 * @param string $name the name of the file without the suffix
	 * @return void
	 */
	function add3rdPartyScript($name);


	/**
	 * @deprecated 8.0.0 include javascript and css in template files
	 * shorthand for addStyle for files in the 3rdparty directory
	 * @param string $name the name of the file without the suffix
	 * @return void
	 */
	function add3rdPartyStyle($name);


	/**
	 * Checks if an app is enabled
	 * @deprecated 8.0.0 communication between apps should happen over built in
	 * callbacks or interfaces (check the contacts and calendar managers)
	 * Checks if an app is enabled
	 * also use \OC::$server->getAppManager()->isEnabledForUser($appName)
	 * @param string $appName the name of an app
	 * @return bool true if app is enabled
	 */
	public function isAppEnabled($appName);

}
