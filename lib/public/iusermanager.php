<?php

namespace OCP;


/**
 * Class Manager
 *
 * Hooks available in scope \OC\User:
 * - preSetPassword(\OC\User\User $user, string $password, string $recoverPassword)
 * - postSetPassword(\OC\User\User $user, string $password, string $recoverPassword)
 * - preDelete(\OC\User\User $user)
 * - postDelete(\OC\User\User $user)
 * - preCreateUser(string $uid, string $password)
 * - postCreateUser(\OC\User\User $user, string $password)
 *
 * @package OC\User
 * @since 8.0.0
 */
interface IUserManager {
		/**
	 * register a user backend
	 *
	 * @param \OCP\UserInterface $backend
	 * @since 8.0.0
	 */
	public function registerBackend($backend);

	/**
	 * Get the active backends
	 * @return \OCP\UserInterface[]
	 * @since 8.0.0
	 */
	public function getBackends();

	/**
	 * remove a user backend
	 *
	 * @param \OCP\UserInterface $backend
	 * @since 8.0.0
	 */
	public function removeBackend($backend);

	/**
	 * remove all user backends
	 * @since 8.0.0
	 */
	public function clearBackends() ;

	/**
	 * get a user by user id
	 *
	 * @param string $uid
	 * @return \OCP\IUser|null Either the user or null if the specified user does not exist
	 * @since 8.0.0
	 */
	public function get($uid);

	/**
	 * check if a user exists
	 *
	 * @param string $uid
	 * @return bool
	 * @since 8.0.0
	 */
	public function userExists($uid);

	/**
	 * Check if the password is valid for the user
	 *
	 * @param string $loginname
	 * @param string $password
	 * @return mixed the User object on success, false otherwise
	 * @since 8.0.0
	 */
	public function checkPassword($loginname, $password);

	/**
	 * search by user id
	 *
	 * @param string $pattern
	 * @param int $limit
	 * @param int $offset
	 * @return \OCP\IUser[]
	 * @since 8.0.0
	 */
	public function search($pattern, $limit = null, $offset = null);

	/**
	 * search by displayName
	 *
	 * @param string $pattern
	 * @param int $limit
	 * @param int $offset
	 * @return \OCP\IUser[]
	 * @since 8.0.0
	 */
	public function searchDisplayName($pattern, $limit = null, $offset = null);

	/**
	 * @param string $uid
	 * @param string $password
	 * @throws \Exception
	 * @return bool|\OCP\IUser the created user of false
	 * @since 8.0.0
	 */
	public function createUser($uid, $password);

	/**
	 * returns how many users per backend exist (if supported by backend)
	 *
	 * @return array an array of backend class as key and count number as value
	 * @since 8.0.0
	 */
	public function countUsers();
}
