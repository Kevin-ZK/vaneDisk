<?php

namespace OC\Files\Cache;

/**
 * check the storage backends for updates and change the cache accordingly
 */
class Shared_Watcher extends Watcher {

	/**
	 * check $path for updates
	 *
	 * @param string $path
	 * @param array $cachedEntry
	 * @return boolean true if path was updated
	 */
	public function checkUpdate($path, $cachedEntry = null) {
		if (parent::checkUpdate($path, $cachedEntry) === true) {
			// since checkUpdate() has already updated the size of the subdirs,
			// only apply the update to the owner's parent dirs

			// find last parent before reaching the shared storage root,
			// which is the actual shared dir from the owner
			$sepPos = strpos($path, '/');
			if ($sepPos > 0) {
				$baseDir = substr($path, 0, $sepPos);
			} else {
				$baseDir = $path;
			}

			// find the path relative to the data dir
			$file = $this->storage->getFile($baseDir);
			$view = new \OC\Files\View('/' . $file['fileOwner']);

			// find the owner's storage and path
			list($storage, $internalPath) = $view->resolvePath($file['path']);

			// update the parent dirs' sizes in the owner's cache
			$storage->getCache()->correctFolderSize(dirname($internalPath));

			return true;
		}
		return false;
	}

	/**
	 * remove deleted files in $path from the cache
	 *
	 * @param string $path
	 */
	public function cleanFolder($path) {
		if ($path != '') {
			parent::cleanFolder($path);
		}
	}

}
