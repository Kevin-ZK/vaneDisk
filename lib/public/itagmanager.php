<?php
namespace OCP;

/**
 * Factory class creating instances of \OCP\ITags
 *
 * A tag can be e.g. 'Family', 'Work', 'Chore', 'Special Occation' or
 * anything else that is either parsed from a vobject or that the user chooses
 * to add.
 * Tag names are not case-sensitive, but will be saved with the case they
 * are entered in. If a user already has a tag 'family' for a type, and
 * tries to add a tag named 'Family' it will be silently ignored.
 * @since 6.0.0
 */
interface ITagManager {

	/**
	 * Create a new \OCP\ITags instance and load tags from db for the current user.
	 *
	 * @see \OCP\ITags
	 * @param string $type The type identifier e.g. 'contact' or 'event'.
	 * @param array $defaultTags An array of default tags to be used if none are stored.
	 * @param boolean $includeShared Whether to include tags for items shared with this user by others.
	 * @param string $userId user for which to retrieve the tags, defaults to the currently
	 * logged in user
	 * @return \OCP\ITags
	 * @since 6.0.0 - parameter $includeShared and $userId were added in 8.0.0
	*/
	public function load($type, $defaultTags = array(), $includeShared = false, $userId = null);
}
