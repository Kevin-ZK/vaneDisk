<?php

namespace OCP;

/**
 * Interface IGroup
 *
 * @package OCP
 * @since 8.0.0
 */
interface IGroup {
	/**
	 * @return string
	 * @since 8.0.0
	 */
	public function getGID();

	/**
	 * get all users in the group
	 *
	 * @return \OCP\IUser[]
	 * @since 8.0.0
	 */
	public function getUsers();

	/**
	 * check if a user is in the group
	 *
	 * @param \OCP\IUser $user
	 * @return bool
	 * @since 8.0.0
	 */
	public function inGroup($user);

	/**
	 * add a user to the group
	 *
	 * @param \OCP\IUser $user
	 * @since 8.0.0
	 */
	public function addUser($user);

	/**
	 * remove a user from the group
	 *
	 * @param \OCP\IUser $user
	 * @since 8.0.0
	 */
	public function removeUser($user);

	/**
	 * search for users in the group by userid
	 *
	 * @param string $search
	 * @param int $limit
	 * @param int $offset
	 * @return \OCP\IUser[]
	 * @since 8.0.0
	 */
	public function searchUsers($search, $limit = null, $offset = null);

	/**
	 * returns the number of users matching the search string
	 *
	 * @param string $search
	 * @return int|bool
	 * @since 8.0.0
	 */
	public function count($search = '');

	/**
	 * search for users in the group by displayname
	 *
	 * @param string $search
	 * @param int $limit
	 * @param int $offset
	 * @return \OCP\IUser[]
	 * @since 8.0.0
	 */
	public function searchDisplayName($search, $limit = null, $offset = null);

	/**
	 * delete the group
	 *
	 * @return bool
	 * @since 8.0.0
	 */
	public function delete();
}
