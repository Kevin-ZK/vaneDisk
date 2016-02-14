<?php
/**
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU AFFERO GENERAL PUBLIC LICENSE
 * License as published by the Free Software Foundation; either
 * version 3 of the License, or any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU AFFERO GENERAL PUBLIC LICENSE for more details.
 *
 * You should have received a copy of the GNU Affero General Public
 * License along with this library.  If not, see <http://www.gnu.org/licenses/>.
 *
 */

namespace OCA\Files_Locking;

use OC\Files\Filesystem;
use OCP\Files\LockNotAcquiredException;

/**
 * Class Lock
 *
 * @package OC\Files
 */
class Lock {
	const READ = 1;
	const WRITE = 2;

	/** @var int $retries Number of lock retries to attempt */
	public static $retries = 40;

	/** @var int $retryInterval Milliseconds between retries */
	public static $retryInterval = 50;

	/** @var string $locksDir Lock directory */
	protected static $locksDir = '';

	/** @var string $path Filename of the file as represented in storage */
	protected $path;

	/** @var array $stack A stack of lock data */
	protected $stack = array();

	/** @var resource $handle A file handle used to maintain a lock */
	protected $handle;

	/** @var string $lockFile Filename of the lock file */
	protected $lockFile;

	/** @var resource $lockFileHandle The file handle used to maintain a lock on the lock file */
	protected $lockFileHandle;

	/** @var \OCP\ILogger */
	protected $log;

	/**
	 * Constructor for the lock instance
	 *
	 * @param string $path Absolute pathname for a local file on which to obtain a lock
	 */
	public function __construct($path) {
		$this->path = Filesystem::normalizePath($path, true, true);
		$this->log = \OC::$server->getLogger();
	}

	/**
	 * @return string
	 */
	public function getPath() {
		return $this->path;
	}

	/**
	 * Acquire read lock on the current file.
	 * If no existing handle is given, fopen() will be called
	 * on the file to obtain one.
	 *
	 * @param resource $existingHandle existing file handle, defaults to null
	 *
	 * @return bool true if the lock could be obtained, false if a timeout
	 * occurred or the file could not be opened
	 */
	protected function obtainReadLock($existingHandle = null) {
		$this->log->debug(sprintf('INFO: Read lock requested for %s', $this->path), ['app' => 'files_locking']);
		$timeout = Lock::$retries;

		// Re-use an existing handle or get a new one
		if (empty($existingHandle)) {
			$handle = fopen($this->path, 'r');
			if ($handle === false) {
				return false;
			}
		} else {
			$handle = $existingHandle;
		}

		if ($this->isLockFileLocked($this->getLockFile($this->path))) {
			$this->log->debug(sprintf('INFO: Read lock has locked lock file %s for %s', $this->getLockFile($this->path), $this->path), ['app' => 'files_locking']);

			while ($this->isLockFileLocked($this->getLockFile($this->path)) && $timeout > 0) {
				usleep(Lock::$retryInterval * 1000);
				$timeout--;
			}
			$this->log->debug(sprintf('INFO: Lock file %s has become unlocked for %s', $this->getLockFile($this->path), $this->path), ['app' => 'files_locking']);
		} else {
			while ((!$lockReturn = flock($handle, LOCK_SH | LOCK_NB, $wouldBlock)) && $timeout > 0) {
				usleep(Lock::$retryInterval * 1000);
				$timeout--;
			}
			if ($wouldBlock == true || $lockReturn == false || $timeout <= 0) {
				$this->log->debug(sprintf('FAIL: Failed to acquire read lock for %s', $this->path), ['app' => 'files_locking']);
				return false;
			}
		}
		if (!$existingHandle) {
			$this->handle = $handle;
		}
		$this->log->debug(sprintf('PASS: Acquired read lock for %s', $this->path), ['app' => 'files_locking']);
		return true;
	}

	/**
	 * Acquire write lock on the current file.
	 * If no existing handle is given, fopen() will be called
	 * on the file to obtain one.
	 *
	 * @param resource $existingHandle existing file handle, defaults to null
	 *
	 * @return bool true if the lock could be obtained, false if a timeout
	 * occurred or the file could not be opened
	 */
	protected function obtainWriteLock($existingHandle = null) {
		$this->log->debug(sprintf('INFO: Write lock requested for %s', $this->path), ['app' => 'files_locking']);

		// Re-use an existing handle or get a new one
		if (empty($existingHandle)) {
			$handle = fopen($this->path, 'c');
			if ($handle === false) {
				return false;
			}
		} else {
			$handle = $existingHandle;
		}

		// If the file doesn't exist, but we can create a lock for it
		if (!file_exists($this->path) && $this->lockLockFile($this->path)) {
			$lockReturn = flock($handle, LOCK_EX | LOCK_NB, $wouldBlock);
			if ($lockReturn == false || $wouldBlock == true) {
				$this->log->debug(sprintf('FAIL: Write lock failed, unable to exclusively lock new file %s', $this->path), ['app' => 'files_locking']);
				return false;
			}
			$this->handle = $handle;
			return true;
		}


		// Since this file does exist, wait for locks to release to get an exclusive lock
		$timeout = Lock::$retries;
		$haveBlock = false;
		while ((!$lockReturn = flock($handle, LOCK_EX | LOCK_NB, $wouldBlock)) && $timeout > 0) {
			// We don't have a lock on the original file, try to get a lock on its lock file
			if ($haveBlock || $haveBlock = $this->lockLockFile($this->lockFile)) {
				usleep(Lock::$retryInterval * 1000);
			} else {
				$this->log->debug(sprintf('FAIL: Write lock failed, unable to lock original %s or lock file', $this->path), ['app' => 'files_locking']);
				return false;
			}
			$timeout--;
		}
		if ($wouldBlock == true || $lockReturn == false) {
			$this->log->debug(sprintf('FAIL: Write lock failed due to timeout on %s', $this->path), ['app' => 'files_locking']);
			return false;
		}
		if (!$existingHandle) {
			$this->handle = $handle;
		}
		$this->log->debug(sprintf('PASS: Write lock succeeded on %s', $this->path), ['app' => 'files_locking']);

		return true;
	}

	/**
	 * Create a lock file and lock it
	 * Sets $this->lockFile to the specified lock file, indicating that the lock file is IN USE for this lock instance
	 * Also sets $this->lockFileHandle to a file handle of the lock file
	 *
	 * @param string $filename The name of the file to lock
	 * @return bool False if lock can't be acquired, true if it can.
	 */
	protected function lockLockFile($filename) {
		$lockFile = $this->getLockFile($filename);
		$this->log->debug(sprintf('INFO: Locking lock file %s for %s', $lockFile, $filename), ['app' => 'files_locking']);

		// If we already manage the lock file, success
		if (!empty($this->lockFile)) {
			$this->log->debug(sprintf('PASS: Lock file %s was locked by this request for %s', $lockFile, $filename), ['app' => 'files_locking']);
			return true;
		}

		// Check if the lock file exists, and if not, try to create it
		$this->log->debug(sprintf('INFO: Does lock file %s already exist?  %s', $lockFile, file_exists($lockFile) ? 'yes' : 'no'), ['app' => 'files_locking']);
		$handle = fopen($lockFile, 'c');
		if (!$handle) {
			$this->log->debug(sprintf('FAIL: Could not create lock file %s', $lockFile), ['app' => 'files_locking']);
			return false;
		}

		// Attempt to acquire lock on lock file
		$wouldBlock = false;
		$timeout = self::$retries;
		// Wait for lock over timeout
		while ((!$lockReturn = flock($handle, LOCK_EX | LOCK_NB, $wouldBlock)) && $timeout > 0) {
			$this->log->debug(sprintf('FAIL: Could not acquire lock on lock file %s, %s timeout increments remain.', $lockFile, $timeout), ['app' => 'files_locking']);
			usleep(self::$retryInterval * 1000);
			$timeout--;
		}
		if ($wouldBlock == true || $lockReturn == false) {
			$this->log->debug(sprintf('FAIL: Could not acquire lock on lock file %s', $lockFile), ['app' => 'files_locking']);
			return false;
		}
		fwrite($handle, $filename);
		$this->log->debug(sprintf('PASS: Wrote filename to lock lock file %s', $lockFile), ['app' => 'files_locking']);

		$this->lockFile = $lockFile;
		$this->lockFileHandle = $handle;

		return true;
	}

	/**
	 * Add a lock of a specific type to the stack
	 *
	 * @param integer $lockType A constant representing the type of lock to queue
	 * @param null|resource $existingHandle An existing file handle from an fopen()
	 * @throws LockNotAcquiredException
	 */
	public function addLock($lockType, $existingHandle = null) {
		if (!isset($this->stack[$lockType])) {
			switch ($lockType) {
				case Lock::READ:
					$result = $this->obtainReadLock($existingHandle);
					break;
				case Lock::WRITE:
					$result = $this->obtainWriteLock($existingHandle);
					break;
				default:
					$result = false;
					break;
			}
			if ($result) {
				$this->stack[$lockType] = 0;
			} else {
				throw new LockNotAcquiredException($this->path, $lockType);
			}
		}

		$this->log->debug(sprintf('INFO: Incrementing lock type %d count for %s', $lockType, $this->path), ['app' => 'files_locking']);
		$this->stack[$lockType]++;

	}

	/**
	 * Release locks on handles and files
	 *
	 * @param int $lockType
	 * @return bool
	 */
	public function release($lockType) {
		if (isset($this->stack[$lockType])) {
			$this->stack[$lockType]--;
			if ($this->stack[$lockType] <= 0) {
				unset($this->stack[$lockType]);
			}
		}

		if (count($this->stack) == 0) {
			// No more locks needed on this file, release the handle and/or lock file
			$this->releaseAll();
		}

		return true;
	}


	/**
	 * Get the lock file associated to a file
	 *
	 * @param string $filename The filename of the file to create a lock file for
	 * @return string The filename of the lock file
	 */
	public static function getLockFile($filename) {
		if (!self::$locksDir) {
			$dataDir = \OC::$server->getConfig()->getSystemValue('datadirectory');
			self::$locksDir = $dataDir . '/.locks';
		}

		if (!file_exists(self::$locksDir)) {
			mkdir(self::$locksDir);
		}

		$filename = Filesystem::normalizePath($filename);
		return self::$locksDir . '/' . sha1($filename) . '.lock';
	}

	/**
	 * Determine if a file has an associated and flocked lock file
	 *
	 * @param string $lockFile The filename of the lock file to check
	 * @return bool True if the lock file is flocked
	 */
	protected function isLockFileLocked($lockFile) {
		if (file_exists($lockFile)) {
			if ($handle = fopen($lockFile, 'c')) {
				if ($lock = flock($handle, LOCK_EX | LOCK_NB)) {
					// Got lock, not blocking, release and unlink
					unlink($lockFile);
					fclose($handle);
					flock($handle, LOCK_UN);
					return false;
				} else {
					return true;
				}
			} else {
				return true;
			}
		}
		return false;
	}

	/**
	 * Release all queued locks on the file
	 *
	 * @return bool
	 */
	public function releaseAll() {
		$this->stack = array();
		$this->log->debug(sprintf('INFO: Releasing locks on %s', $this->path), ['app' => 'files_locking']);
		if (!empty($this->handle) && is_resource($this->handle)) {
			flock($this->handle, LOCK_UN);
			fclose($this->handle);
			$this->log->debug(sprintf('INFO: Released lock handle %s on %s', $this->handle, $this->path), ['app' => 'files_locking']);
			$this->handle = null;
		}
		if (!empty($this->lockFile) && file_exists($this->lockFile)) {
			if (!empty($this->lockFileHandle) && is_resource($this->lockFileHandle)) {
				fclose($this->lockFileHandle);
				$this->lockFileHandle = null;
			}
			unlink($this->lockFile);
			$this->log->debug(sprintf('INFO: Released lock file %s on %s', $this->lockFile, $this->path), ['app' => 'files_locking']);
			$this->lockFile = null;
		}
		$this->log->debug(sprintf('FREE: Released locks on %s', $this->path), ['app' => 'files_locking']);
		return true;
	}

	public function __destruct() {
		// Only releaseAll if we have locks to release
		if (!empty($this->handle) || !empty($this->lockFile)) {
			$this->log->debug(sprintf('INFO: Destroying locks on %s', $this->path), ['app' => 'files_locking']);
			$this->releaseAll();
		}
	}

}
