<?php

// use OCP namespace for all classes that are considered public.
// This means that they should be used by apps instead of the internal vanedisk classes
namespace OCP;

/**
 * This class provides functions to manage apps in vanedisk
 * @since 5.0.0
 */
class API {

	/**
	 * API authentication levels
	 * @since 8.1.0
	 */
	const GUEST_AUTH = 0;
	const USER_AUTH = 1;
	const SUBADMIN_AUTH = 2;
	const ADMIN_AUTH = 3;

	/**
	 * API Response Codes
	 * @since 8.1.0
	 */
	const RESPOND_UNAUTHORISED = 997;
	const RESPOND_SERVER_ERROR = 996;
	const RESPOND_NOT_FOUND = 998;
	const RESPOND_UNKNOWN_ERROR = 999;

	/**
	 * registers an api call
	 * @param string $method the http method
	 * @param string $url the url to match
	 * @param callable $action the function to run
	 * @param string $app the id of the app registering the call
	 * @param int $authLevel the level of authentication required for the call (See `self::*_AUTH` constants)
	 * @param array $defaults
	 * @param array $requirements
	 * @since 5.0.0
	 */
	public static function register($method, $url, $action, $app, $authLevel = self::USER_AUTH,
		$defaults = array(), $requirements = array()){
		\OC_API::register($method, $url, $action, $app, $authLevel, $defaults, $requirements);
	}

}
