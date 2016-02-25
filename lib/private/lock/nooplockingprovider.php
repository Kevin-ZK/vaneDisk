<?php

namespace OC\Lock;

use OCP\Lock\ILockingProvider;

/**
 * Locking provider that does nothing.
 *
 * To be used when locking is disabled.
 */
class NoopLockingProvider implements ILockingProvider {

    /**
     * {@inheritdoc}
     */
	public function isLocked($path, $type) {
		return false;
	}

    /**
     * {@inheritdoc}
     */
	public function acquireLock($path, $type) {
		// do nothing
	}

	/**
     * {@inheritdoc}
	 */
	public function releaseLock($path, $type) {
		// do nothing
	}

	/**1
	 * {@inheritdoc}
	 */
	public function releaseAll() {
		// do nothing
	}

	/**
	 * {@inheritdoc}
	 */
	public function changeLock($path, $targetType) {
		// do nothing
	}
}
