<?php

namespace OCP;

/**
 * Interface IUser
 *
 * @package OCP
 * @since 8.0.0
 */
interface IUser {
	/**
	 * get the user id
	 *
	 * @return string
	 * @since 8.0.0
	 */
	public function getUID();

	/**
	 * get the display name for the user, if no specific display name is set it will fallback to the user id
	 *
	 * @return string
	 * @since 8.0.0
	 */
	public function getDisplayName();

	/**
	 * set the display name for the user
	 *
	 * @param string $displayName
	 * @return bool
	 * @since 8.0.0
	 */
	public function setDisplayName($displayName);

	/**
	 * returns the timestamp of the user's last login or 0 if the user did never
	 * login
	 *
	 * @return int
	 * @since 8.0.0
	 */
	public function getLastLogin();

	/**
	 * updates the timestamp of the most recent login of this user
	 * @since 8.0.0
	 */
	public function updateLastLoginTimestamp();

	/**
	 * Delete the user
	 *
	 * @return bool
	 * @since 8.0.0
	 */
	public function delete();

	/**
	 * Set the password of the user
	 *
	 * @param string $password
	 * @param string $recoveryPassword for the encryption app to reset encryption keys
	 * @return bool
	 * @since 8.0.0
	 */
	public function setPassword($password, $recoveryPassword = null);

	/**
	 * get the users home folder to mount
	 *
	 * @return string
	 * @since 8.0.0
	 */
	public function getHome();

	/**
	 * Get the name of the backend class the user is connected with
	 *
	 * @return string
	 * @since 8.0.0
	 */
	public function getBackendClassName();

	/**
	 * check if the backend allows the user to change his avatar on Personal page
	 *
	 * @return bool
	 * @since 8.0.0
	 */
	public function canChangeAvatar();

	/**
	 * check if the backend supports changing passwords
	 *
	 * @return bool
	 * @since 8.0.0
	 */
	public function canChangePassword();

	/**
	 * check if the backend supports changing display names
	 *
	 * @return bool
	 * @since 8.0.0
	 */
	public function canChangeDisplayName();

	/**
	 * check if the user is enabled
	 *
	 * @return bool
	 * @since 8.0.0
	 */
	public function isEnabled();

	/**
	 * set the enabled status for the user
	 *
	 * @param bool $enabled
	 * @since 8.0.0
	 */
	public function setEnabled($enabled);
}
