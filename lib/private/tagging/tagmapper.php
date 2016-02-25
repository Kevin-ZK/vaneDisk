<?php

namespace OC\Tagging;

use \OCP\AppFramework\Db\Mapper,
    \OCP\AppFramework\Db\DoesNotExistException,
    \OCP\IDBConnection;

/**
 * Mapper for Tag entity
 */
class TagMapper extends Mapper {

	/**
	* Constructor.
	*
	* @param IDBConnection $db Instance of the Db abstraction layer.
	*/
	public function __construct(IDBConnection $db) {
		parent::__construct($db, 'vcategory', 'OC\Tagging\Tag');
	}

	/**
	* Load tags from the database.
	*
	* @param array|string $owners The user(s) whose tags we are going to load.
	* @param string $type The type of item for which we are loading tags.
	* @return array An array of Tag objects.
	*/
	public function loadTags($owners, $type) {
		if(!is_array($owners)) {
			$owners = array($owners);
		}

		$sql = 'SELECT `id`, `uid`, `type`, `category` FROM `' . $this->getTableName() . '` '
			. 'WHERE `uid` IN (' . str_repeat('?,', count($owners)-1) . '?) AND `type` = ? ORDER BY `category`';
		return $this->findEntities($sql, array_merge($owners, array($type)));
	}

	/**
	* Check if a given Tag object already exists in the database.
	*
	* @param Tag $tag The tag to look for in the database.
	* @return bool
	*/
	public function tagExists($tag) {
		$sql = 'SELECT `id`, `uid`, `type`, `category` FROM `' . $this->getTableName() . '` '
			. 'WHERE `uid` = ? AND `type` = ? AND `category` = ?';
		try {
			$this->findEntity($sql, array($tag->getOwner(), $tag->getType(), $tag->getName()));
		} catch (DoesNotExistException $e) {
			return false;
		}
		return true;
	}
}

