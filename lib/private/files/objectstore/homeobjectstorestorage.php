<?php

namespace OC\Files\ObjectStore;

use OC\User\User;

class HomeObjectStoreStorage extends ObjectStoreStorage implements \OCP\Files\IHomeStorage {

	/**
	 * The home user storage requires a user object to create a unique storage id
	 * @param array $params
	 */
	public function __construct($params) {
		if ( ! isset($params['user']) || ! $params['user'] instanceof User) {
			throw new \Exception('missing user object in parameters');
		}
		$this->user = $params['user'];
		parent::__construct($params);
	}

	public function getId () {
		return 'object::user:' . $this->user->getUID();
	}

	/**
	 * get the owner of a path
	 *
	 * @param string $path The path to get the owner
	 * @return false|string uid
	 */
	public function getOwner($path) {
		if (is_object($this->user)) {
			return $this->user->getUID();
		}
		return false;
	}

	/**
	 * @param string $path, optional
	 * @return \OC\User\User
	 */
	public function getUser($path = null) {
		return $this->user;
	}


}
