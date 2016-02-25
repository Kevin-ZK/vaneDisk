<?php

interface OC_Group_Interface {
	/**
	* Check if backend implements actions
	* @param int $actions bitwise-or'ed actions
	* @return boolean
	*
	* Returns the supported actions as int to be
	* compared with \OC_Group_Backend::CREATE_GROUP etc.
	*/
	public function implementsActions($actions);

	/**
	 * is user in group?
	 * @param string $uid uid of the user
	 * @param string $gid gid of the group
	 * @return bool
	 *
	 * Checks whether the user is member of a group or not.
	 */
	public function inGroup($uid, $gid);

	/**
	 * Get all groups a user belongs to
	 * @param string $uid Name of the user
	 * @return array an array of group names
	 *
	 * This function fetches all groups a user belongs to. It does not check
	 * if the user exists at all.
	 */
	public function getUserGroups($uid);

	/**
	 * get a list of all groups
	 * @param string $search
	 * @param int $limit
	 * @param int $offset
	 * @return array an array of group names
	 *
	 * Returns a list with all groups
	 */
	public function getGroups($search = '', $limit = -1, $offset = 0);

	/**
	 * check if a group exists
	 * @param string $gid
	 * @return bool
	 */
	public function groupExists($gid);

	/**
	 * get a list of all users in a group
	 * @param string $gid
	 * @param string $search
	 * @param int $limit
	 * @param int $offset
	 * @return array an array of user ids
	 */
	public function usersInGroup($gid, $search = '', $limit = -1, $offset = 0);

}
