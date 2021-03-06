<?php

/**
 * Factory class creating instances of \OCP\ITags
 *
 * A tag can be e.g. 'Family', 'Work', 'Chore', 'Special Occation' or
 * anything else that is either parsed from a vobject or that the user chooses
 * to add.
 * Tag names are not case-sensitive, but will be saved with the case they
 * are entered in. If a user already has a tag 'family' for a type, and
 * tries to add a tag named 'Family' it will be silently ignored.
 */

namespace OC;

use OC\Tagging\TagMapper;

class TagManager implements \OCP\ITagManager {

	/**
	 * User session
	 *
	 * @var \OCP\IUserSession
	 */
	private $userSession;

	/**
	 * TagMapper
	 *
	 * @var TagMapper
	 */
	private $mapper;

	/**
	* Constructor.
	*
	* @param TagMapper $mapper Instance of the TagMapper abstraction layer.
	* @param \OCP\IUserSession $userSession the user session
	*/
	public function __construct(TagMapper $mapper, \OCP\IUserSession $userSession) {
		$this->mapper = $mapper;
		$this->userSession = $userSession;

	}

	/**
	* Create a new \OCP\ITags instance and load tags from db.
	*
	* @see \OCP\ITags
	* @param string $type The type identifier e.g. 'contact' or 'event'.
	* @param array $defaultTags An array of default tags to be used if none are stored.
	* @param boolean $includeShared Whether to include tags for items shared with this user by others.
	* @param string $userId user for which to retrieve the tags, defaults to the currently
	* logged in user
	* @return \OCP\ITags
	*/
	public function load($type, $defaultTags = array(), $includeShared = false, $userId = null) {
		if (is_null($userId)) {
			$user = $this->userSession->getUser();
			if ($user === null) {
				// nothing we can do without a user
				return null;
			}
			$userId = $this->userSession->getUser()->getUId();
		}
		return new Tags($this->mapper, $userId, $type, $defaultTags, $includeShared);
	}

}
