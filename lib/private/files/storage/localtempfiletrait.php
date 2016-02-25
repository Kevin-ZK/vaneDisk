<?php

namespace OC\Files\Storage;

/**
 * Storage backend class for providing common filesystem operation methods
 * which are not storage-backend specific.
 *
 * \OC\Files\Storage\Common is never used directly; it is extended by all other
 * storage backends, where its methods may be overridden, and additional
 * (backend-specific) methods are defined.
 *
 * Some \OC\Files\Storage\Common methods call functions which are first defined
 * in classes which extend it, e.g. $this->stat() .
 */
trait LocalTempFileTrait {

	/** @var string[] */
	protected $cachedFiles = [];

	/**
	 * @param string $path
	 * @return string
	 */
	protected function getCachedFile($path) {
		if (!isset($this->cachedFiles[$path])) {
			$this->cachedFiles[$path] = $this->toTmpFile($path);
		}
		return $this->cachedFiles[$path];
	}

	/**
	 * @param string $path
	 */
	protected function removeCachedFile($path) {
		unset($this->cachedFiles[$path]);
	}

	/**
	 * @param string $path
	 * @return string
	 */
	protected function toTmpFile($path) { //no longer in the storage api, still useful here
		$source = $this->fopen($path, 'r');
		if (!$source) {
			return false;
		}
		if ($pos = strrpos($path, '.')) {
			$extension = substr($path, $pos);
		} else {
			$extension = '';
		}
		$tmpFile = \OC_Helper::tmpFile($extension);
		$target = fopen($tmpFile, 'w');
		\OC_Helper::streamCopy($source, $target);
		fclose($target);
		return $tmpFile;
	}
}
