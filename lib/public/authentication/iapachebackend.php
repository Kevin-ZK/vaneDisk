<?php
namespace OCP\Authentication;

/**
 * Interface IApacheBackend
 *
 * @package OCP\Authentication
 * @since 6.0.0
 */
interface IApacheBackend {

	/**
	 * In case the user has been authenticated by Apache true is returned.
	 *
	 * @return boolean whether Apache reports a user as currently logged in.
	 * @since 6.0.0
	 */
	public function isSessionActive();

	/**
	 * Creates an attribute which is added to the logout hyperlink. It can
	 * supply any attribute(s) which are valid for <a>.
	 *
	 * @return string with one or more HTML attributes.
	 * @since 6.0.0
	 */
	public function getLogoutAttribute();

	/**
	 * Return the id of the current user
	 * @return string
	 * @since 6.0.0
	 */
	public function getCurrentUserId();

}
