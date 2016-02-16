<?php
/**
 *
 * @copyright Copyright (c) 2015, ownCloud, Inc.
 * @license AGPL-3.0
 *
 * This code is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License, version 3,
 * as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License, version 3,
 * along with this program.  If not, see <http://www.gnu.org/licenses/>
 *
 */

namespace OC;

use OCP\ILogger;
use OCP\ITempManager;

class TempManager implements ITempManager {
	/** @var string[] Current temporary files and folders, used for cleanup */
	protected $current = [];
	/** @var string i.e. /tmp on linux systems */
	protected $tmpBaseDir;
	/** @var ILogger */
	protected $log;
	/** Prefix */
	const TMP_PREFIX = 'oc_tmp_';

	/**
	 * @param string $baseDir
	 * @param \OCP\ILogger $logger
	 */
	public function __construct($baseDir, ILogger $logger) {
		$this->tmpBaseDir = $baseDir;
		$this->log = $logger;
	}

	/**
	 * Builds the filename with suffix and removes potential dangerous characters
	 * such as directory separators.
	 *
	 * @param string $absolutePath Absolute path to the file / folder
	 * @param string $postFix Postfix appended to the temporary file name, may be user controlled
	 * @return string
	 */
	private function buildFileNameWithSuffix($absolutePath, $postFix = '') {
		if($postFix !== '') {
			$postFix = '.' . ltrim($postFix, '.');
			$postFix = str_replace(['\\', '/'], '', $postFix);
			$absolutePath .= '-';
		}

		return $absolutePath . $postFix;
	}

	/**
	 * Create a temporary file and return the path
	 *
	 * @param string $postFix Postfix appended to the temporary file name
	 * @return string
	 */
	public function getTemporaryFile($postFix = '') {
		if (is_writable($this->tmpBaseDir)) {
			// To create an unique file and prevent the risk of race conditions
			// or duplicated temporary files by other means such as collisions
			// we need to create the file using `tempnam` and append a possible
			// postfix to it later
			$file = tempnam($this->tmpBaseDir, self::TMP_PREFIX);
			$this->current[] = $file;

			// If a postfix got specified sanitize it and create a postfixed
			// temporary file
			if($postFix !== '') {
				$fileNameWithPostfix = $this->buildFileNameWithSuffix($file, $postFix);
				touch($fileNameWithPostfix);
				chmod($fileNameWithPostfix, 0600);
				$this->current[] = $fileNameWithPostfix;
				return $fileNameWithPostfix;
			}

			return $file;
		} else {
			$this->log->warning(
				'Can not create a temporary file in directory {dir}. Check it exists and has correct permissions',
				[
					'dir' => $this->tmpBaseDir,
				]
			);
			return false;
		}
	}

	/**
	 * Create a temporary folder and return the path
	 *
	 * @param string $postFix Postfix appended to the temporary folder name
	 * @return string
	 */
	public function getTemporaryFolder($postFix = '') {
		if (is_writable($this->tmpBaseDir)) {
			// To create an unique directory and prevent the risk of race conditions
			// or duplicated temporary files by other means such as collisions
			// we need to create the file using `tempnam` and append a possible
			// postfix to it later
			$uniqueFileName = tempnam($this->tmpBaseDir, self::TMP_PREFIX);
			$this->current[] = $uniqueFileName;

			// Build a name without postfix
			$path = $this->buildFileNameWithSuffix($uniqueFileName . '-folder', $postFix);
			mkdir($path, 0700);
			$this->current[] = $path;

			return $path . '/';
		} else {
			$this->log->warning(
				'Can not create a temporary folder in directory {dir}. Check it exists and has correct permissions',
				[
					'dir' => $this->tmpBaseDir,
				]
			);
			return false;
		}
	}

	/**
	 * Remove the temporary files and folders generated during this request
	 */
	public function clean() {
		$this->cleanFiles($this->current);
	}

	/**
	 * @param string[] $files
	 */
	protected function cleanFiles($files) {
		foreach ($files as $file) {
			if (file_exists($file)) {
				try {
					\OC_Helper::rmdirr($file);
				} catch (\UnexpectedValueException $ex) {
					$this->log->warning(
						"Error deleting temporary file/folder: {file} - Reason: {error}",
						[
							'file' => $file,
							'error' => $ex->getMessage(),
						]
					);
				}
			}
		}
	}

	/**
	 * Remove old temporary files and folders that were failed to be cleaned
	 */
	public function cleanOld() {
		$this->cleanFiles($this->getOldFiles());
	}

	/**
	 * Get all temporary files and folders generated by oc older than an hour
	 *
	 * @return string[]
	 */
	protected function getOldFiles() {
		$cutOfTime = time() - 3600;
		$files = [];
		$dh = opendir($this->tmpBaseDir);
		if ($dh) {
			while (($file = readdir($dh)) !== false) {
				if (substr($file, 0, 7) === self::TMP_PREFIX) {
					$path = $this->tmpBaseDir . '/' . $file;
					$mtime = filemtime($path);
					if ($mtime < $cutOfTime) {
						$files[] = $path;
					}
				}
			}
		}
		return $files;
	}
}