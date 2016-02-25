<?php
namespace OCP;

/**
 * User session
 * @since 6.0.0
 */
interface IUserSession {
	/**
	 * Do a user login
	 * @param string $user the username
	 * @param string $password the password
	 * @return bool true if successful
	 * @since 6.0.0
	 */
	public function login($user, $password);

	/**
	 * Logs the user out including all the session data
	 * Logout, destroys session
	 * @return void
	 * @since 6.0.0
	 */
	public function logout();

	/**
	 * set the currently active user
	 *
	 * @param \OCP\IUser|null $user
	 * @since 8.0.0
	 */
	public function setUser($user);

	/**
	 * get the current active user
	 *
	 * @return \OCP\IUser|null Current user, otherwise null
	 * @since 8.0.0
	 */
	public function getUser();

	/**
	 * Checks whether the user is logged in
	 *
	 * @return bool if logged in
	 * @since 8.0.0
	 */
	public function isLoggedIn();
}
