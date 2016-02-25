<?php

namespace OCA\user_ldap\lib\user;

use OCA\user_ldap\lib\user\OfflineUser;
use OCA\User_LDAP\Mapping\UserMapping;

/**
 * Class DeletedUsersIndex
 * @package OCA\User_LDAP
 */
class DeletedUsersIndex {
	/**
	 * @var \OCP\IConfig $config
	 */
	protected $config;

	/**
	 * @var \OCP\IDBConnection $db
	 */
	protected $db;

	/**
	 * @var \OCA\User_LDAP\Mapping\UserMapping $mapping
	 */
	protected $mapping;

	/**
	 * @var array $deletedUsers
	 */
	protected $deletedUsers;

	/**
	 * @param OCP\IConfig $config
	 * @param OCP\IDBConnection $db
	 * @param OCA\User_LDAP\Mapping\UserMapping $mapping
	 */
	public function __construct(\OCP\IConfig $config, \OCP\IDBConnection $db, UserMapping $mapping) {
		$this->config = $config;
		$this->db = $db;
		$this->mapping = $mapping;
	}

	/**
	 * reads LDAP users marked as deleted from the database
	 * @return OCA\user_ldap\lib\user\OfflineUser[]
	 */
	private function fetchDeletedUsers() {
		$deletedUsers = $this->config->getUsersForUserValue(
			'user_ldap', 'isDeleted', '1');

		$userObjects = array();
		foreach($deletedUsers as $user) {
			$userObjects[] = new OfflineUser($user, $this->config, $this->db, $this->mapping);
		}
		$this->deletedUsers = $userObjects;

		return $this->deletedUsers;
	}

	/**
	 * returns all LDAP users that are marked as deleted
	 * @return OCA\user_ldap\lib\user\OfflineUser[]
	 */
	public function getUsers() {
		if(is_array($this->deletedUsers)) {
			return $this->deletedUsers;
		}
		return $this->fetchDeletedUsers();
	}

	/**
	 * whether at least one user was detected as deleted
	 * @return bool
	 */
	public function hasUsers() {
		if($this->deletedUsers === false) {
			$this->fetchDeletedUsers();
		}
		if(is_array($this->deletedUsers) && count($this->deletedUsers) > 0) {
			return true;
		}
		return false;
	}

	/**
	 * marks a user as deleted
	 * @param string ocName
	 */
	public function markUser($ocName) {
		$this->config->setUserValue($ocName, 'user_ldap', 'isDeleted', '1');
	}
}
