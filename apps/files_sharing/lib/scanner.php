<?php

namespace OC\Files\Cache;

/**
 * Scanner for SharedStorage
 */
class SharedScanner extends Scanner {

	/**
	 * Returns metadata from the shared storage, but
	 * with permissions from the source storage.
	 *
	 * @param string $path path of the file for which to retrieve metadata
	 *
	 * @return array an array of metadata of the file
	 */
	public function getData($path){
		$data = parent::getData($path);
		$sourcePath = $this->storage->getSourcePath($path);
		list($sourceStorage, $internalPath) = \OC\Files\Filesystem::resolvePath($sourcePath);
		$data['permissions'] = $sourceStorage->getPermissions($internalPath);
		return $data;
	}
}

