<?php

namespace OC\Files\Cache;

class HomeCache extends Cache {
	/**
	 * get the size of a folder and set it in the cache
	 *
	 * @param string $path
	 * @param array $entry (optional) meta data of the folder
	 * @return int
	 */
	public function calculateFolderSize($path, $entry = null) {
		if ($path !== '/' and $path !== '' and $path !== 'files' and $path !== 'files_trashbin' and $path !== 'files_versions') {
			return parent::calculateFolderSize($path, $entry);
		} elseif ($path === '' or $path === '/') {
			// since the size of / isn't used (the size of /files is used instead) there is no use in calculating it
			return 0;
		}

		$totalSize = 0;
		if (is_null($entry)) {
			$entry = $this->get($path);
		}
		if ($entry && $entry['mimetype'] === 'httpd/unix-directory') {
			$id = $entry['fileid'];
			$sql = 'SELECT SUM(`size`) AS f1 ' .
			   'FROM `*PREFIX*filecache` ' .
				'WHERE `parent` = ? AND `storage` = ? AND `size` >= 0';
			$result = \OC_DB::executeAudited($sql, array($id, $this->getNumericStorageId()));
			if ($row = $result->fetchRow()) {
				$result->closeCursor();
				list($sum) = array_values($row);
				$totalSize = 0 + $sum;
				$entry['size'] += 0;
				if ($entry['size'] !== $totalSize) {
					$this->update($id, array('size' => $totalSize));
				}
			}
		}
		return $totalSize;
	}

	/**
	 * @param string $path
	 * @return array
	 */
	public function get($path) {
		$data = parent::get($path);
		if ($path === '' or $path === '/') {
			// only the size of the "files" dir counts
			$filesData = parent::get('files');

			if (isset($filesData['size'])) {
				$data['size'] = $filesData['size'];
			}
		}
		return $data;
	}
}
