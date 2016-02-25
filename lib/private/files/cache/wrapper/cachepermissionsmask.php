<?php

namespace OC\Files\Cache\Wrapper;

class CachePermissionsMask extends CacheWrapper {
	/**
	 * @var int
	 */
	protected $mask;

	/**
	 * @param \OC\Files\Cache\Cache $cache
	 * @param int $mask
	 */
	public function __construct($cache, $mask) {
		parent::__construct($cache);
		$this->mask = $mask;
	}

	protected function formatCacheEntry($entry) {
		if (isset($entry['permissions'])) {
			$entry['permissions'] &= $this->mask;
		}
		return $entry;
	}
}
