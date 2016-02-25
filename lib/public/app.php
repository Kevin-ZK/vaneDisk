<?php
namespace OCP;

class App {
	/**
	 * Makes ownCloud aware of this app
	 * @param array $data with all information
	 * @return boolean
	 *
	 * @deprecated 4.5.0 This method is deprecated. Do not call it anymore.
	 * It'll remain in our public API for compatibility reasons.
	 *
	 */
	public static function register( $data ) {
		return true; // don't do anything
	}

	/**
	 * Adds an entry to the navigation
	 *
	 * This function adds a new entry to the navigation visible to users. $data
	 * is an associative array.
	 * The following keys are required:
	 *   - id: unique id for this entry ('addressbook_index')
	 *   - href: link to the page
	 *   - name: Human readable name ('Addressbook')
	 *
	 * The following keys are optional:
	 *   - icon: path to the icon of the app
	 *   - order: integer, that influences the position of your application in
	 *     the navigation. Lower values come first.
	 *
	 * @param array $data containing the data
	 * @return boolean
	 *
	 * @deprecated 8.1.0 Use \OC::$server->getNavigationManager()->add() instead to
	 * register a closure, this helps to speed up all requests against ownCloud
	 * @since 4.0.0
	 */
	public static function addNavigationEntry($data) {
		\OC::$server->getNavigationManager()->add($data);
		return true;
	}

	/**
	 * Marks a navigation entry as active
	 * @param string $id id of the entry
	 * @return boolean
	 *
	 * This function sets a navigation entry as active and removes the 'active'
	 * property from all other entries. The templates can use this for
	 * highlighting the current position of the user.
	 *
	 * @deprecated 8.1.0 Use \OC::$server->getNavigationManager()->setActiveEntry() instead
	 * @since 4.0.0
	 */
	public static function setActiveNavigationEntry( $id ) {
		return \OC_App::setActiveNavigationEntry( $id );
	}

	/**
	 * Register a Configuration Screen that should appear in the personal settings section.
	 * @param string $app appid
	 * @param string $page page to be included
	 * @return void
	 * @since 4.0.0
	*/
	public static function registerPersonal( $app, $page ) {
		\OC_App::registerPersonal( $app, $page );
	}

	/**
	 * Register a Configuration Screen that should appear in the Admin section.
	 * @param string $app string appid
	 * @param string $page string page to be included
	 * @return void
	 * @since 4.0.0
	 */
	public static function registerAdmin( $app, $page ) {
		\OC_App::registerAdmin( $app, $page );
	}

	/**
	 * Read app metadata from the info.xml file
	 * @param string $app id of the app or the path of the info.xml file
	 * @param boolean $path (optional)
	 * @return array
	 * @since 4.0.0
	*/
	public static function getAppInfo( $app, $path=false ) {
		return \OC_App::getAppInfo( $app, $path);
	}

	/**
	 * checks whether or not an app is enabled
	 * @param string $app
	 * @return boolean
	 *
	 * This function checks whether or not an app is enabled.
	 * @since 4.0.0
	 */
	public static function isEnabled( $app ) {
		return \OC_App::isEnabled( $app );
	}

	/**
	 * Check if the app is enabled, redirects to home if not
	 * @param string $app
	 * @return void
	 * @since 4.0.0
	*/
	public static function checkAppEnabled( $app ) {
		\OC_Util::checkAppEnabled( $app );
	}

	/**
	 * Get the last version of the app, either from appinfo/version or from appinfo/info.xml
	 * @param string $app
	 * @return string
	 * @since 4.0.0
	 */
	public static function getAppVersion( $app ) {
		return \OC_App::getAppVersion( $app );
	}
}
