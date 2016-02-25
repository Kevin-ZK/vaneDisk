<?php

namespace OCP\AppFramework;

use OCP\AppFramework\IApi;
use OCP\IContainer;

/**
 * Class IAppContainer
 * @package OCP\AppFramework
 *
 * This container interface provides short cuts for app developers to access predefined app service.
 * @since 6.0.0
 */
interface IAppContainer extends IContainer {

	/**
	 * used to return the appname of the set application
	 * @return string the name of your application
	 * @since 6.0.0
	 */
	function getAppName();

	/**
	 * @deprecated 8.0.0 implements only deprecated methods
	 * @return IApi
	 * @since 6.0.0
	 */
	function getCoreApi();

	/**
	 * @return \OCP\IServerContainer
	 * @since 6.0.0
	 */
	function getServer();

	/**
	 * @param string $middleWare
	 * @return boolean
	 * @since 6.0.0
	 */
	function registerMiddleWare($middleWare);

	/**
	 * @deprecated 8.0.0 use IUserSession->isLoggedIn()
	 * @return boolean
	 * @since 6.0.0
	 */
	function isLoggedIn();

	/**
	 * @deprecated 8.0.0 use IGroupManager->isAdmin($userId)
	 * @return boolean
	 * @since 6.0.0
	 */
	function isAdminUser();

	/**
	 * @deprecated 8.0.0 use the ILogger instead
	 * @param string $message
	 * @param string $level
	 * @return mixed
	 * @since 6.0.0
	 */
	function log($message, $level);

}
