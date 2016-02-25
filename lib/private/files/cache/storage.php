<?php

namespace OC\Files\Cache;

/**
 * Handle the mapping between the string and numeric storage ids
 *
 * Each storage has 2 different ids
 * 	a string id which is generated by the storage backend and reflects the configuration of the storage (e.g. 'smb://user@host/share')
 * 	and a numeric storage id which is referenced in the file cache
 *
 * A mapping between the two storage ids is stored in the database and accessible trough this class
 *
 * @package OC\Files\Cache
 */
class Storage {
	private $storageId;
	private $numericId;

	/**
	 * @param \OC\Files\Storage\Storage|string $storage
	 * @throws \RuntimeException
	 */
	public function __construct($storage) {
		if ($storage instanceof \OC\Files\Storage\Storage) {
			$this->storageId = $storage->getId();
		} else {
			$this->storageId = $storage;
		}
		$this->storageId = self::adjustStorageId($this->storageId);

		$sql = 'SELECT `numeric_id` FROM `*PREFIX*storages` WHERE `id` = ?';
		$result = \OC_DB::executeAudited($sql, array($this->storageId));
		if ($row = $result->fetchRow()) {
			$this->numericId = $row['numeric_id'];
		} else {
			$connection = \OC_DB::getConnection();
			if ($connection->insertIfNotExist('*PREFIX*storages', ['id' => $this->storageId])) {
				$this->numericId = \OC_DB::insertid('*PREFIX*storages');
			} else {
				$result = \OC_DB::executeAudited($sql, array($this->storageId));
				if ($row = $result->fetchRow()) {
					$this->numericId = $row['numeric_id'];
				} else {
					throw new \RuntimeException('Storage could neither be inserted nor be selected from the database');
				}
			}
		}
	}

	/**
	 * Adjusts the storage id to use md5 if too long
	 * @param string $storageId storage id
	 * @return string unchanged $storageId if its length is less than 64 characters,
	 * else returns the md5 of $storageId
	 */
	public static function adjustStorageId($storageId) {
		if (strlen($storageId) > 64) {
			return md5($storageId);
		}
		return $storageId;
	}

	/**
	 * Get the numeric id for the storage
	 *
	 * @return int
	 */
	public function getNumericId() {
		return $this->numericId;
	}

	/**
	 * Get the string id for the storage
	 *
	 * @param int $numericId
	 * @return string|null either the storage id string or null if the numeric id is not known
	 */
	public static function getStorageId($numericId) {

		$sql = 'SELECT `id` FROM `*PREFIX*storages` WHERE `numeric_id` = ?';
		$result = \OC_DB::executeAudited($sql, array($numericId));
		if ($row = $result->fetchRow()) {
			return $row['id'];
		} else {
			return null;
		}
	}

	/**
	 * Get the numeric of the storage with the provided string id
	 *
	 * @param $storageId
	 * @return int|null either the numeric storage id or null if the storage id is not knwon
	 */
	public static function getNumericStorageId($storageId) {
		$storageId = self::adjustStorageId($storageId);

		$sql = 'SELECT `numeric_id` FROM `*PREFIX*storages` WHERE `id` = ?';
		$result = \OC_DB::executeAudited($sql, array($storageId));
		if ($row = $result->fetchRow()) {
			return $row['numeric_id'];
		} else {
			return null;
		}
	}

	/**
	 * Check if a string storage id is known
	 *
	 * @param string $storageId
	 * @return bool
	 */
	public static function exists($storageId) {
		return !is_null(self::getNumericStorageId($storageId));
	}

	/**
	 * remove the entry for the storage
	 *
	 * @param string $storageId
	 */
	public static function remove($storageId) {
		$storageId = self::adjustStorageId($storageId);
		$numericId = self::getNumericStorageId($storageId);
		$sql = 'DELETE FROM `*PREFIX*storages` WHERE `id` = ?';
		\OC_DB::executeAudited($sql, array($storageId));

		if (!is_null($numericId)) {
			$sql = 'DELETE FROM `*PREFIX*filecache` WHERE `storage` = ?';
			\OC_DB::executeAudited($sql, array($numericId));
		}
	}
}
