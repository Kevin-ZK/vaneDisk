<?php

namespace OC\Memcache;

use \OCP\ICacheFactory;

class Factory implements ICacheFactory {
	const NULL_CACHE = '\\OC\\Memcache\\NullCache';

	/**
	 * @var string $globalPrefix
	 */
	private $globalPrefix;

	/**
	 * @var string $localCacheClass
	 */
	private $localCacheClass;

	/**
	 * @var string $distributedCacheClass
	 */
	private $distributedCacheClass;

	/**
	 * @var string $lockingCacheClass
	 */
	private $lockingCacheClass;

	/**
	 * @param string $globalPrefix
	 * @param string|null $localCacheClass
	 * @param string|null $distributedCacheClass
	 * @param string|null $lockingCacheClass
	 */
	public function __construct($globalPrefix,
		$localCacheClass = null, $distributedCacheClass = null, $lockingCacheClass = null)
	{
		$this->globalPrefix = $globalPrefix;

		if (!$localCacheClass) {
			$localCacheClass = self::NULL_CACHE;
		}
		if (!$distributedCacheClass) {
			$distributedCacheClass = $localCacheClass;
		}

		if (!$localCacheClass::isAvailable()) {
			throw new \OC\HintException(
				'Missing memcache class ' . $localCacheClass . ' for local cache',
				'Is the matching PHP module installed and enabled ?'
			);
		}
		if (!$distributedCacheClass::isAvailable()) {
			throw new \OC\HintException(
				'Missing memcache class ' . $distributedCacheClass . ' for distributed cache',
				'Is the matching PHP module installed and enabled ?'
			);
		}
		if (!($lockingCacheClass && $lockingCacheClass::isAvailable())) {
			// dont fallback since the fallback might not be suitable for storing lock
			$lockingCacheClass = '\OC\Memcache\NullCache';
		}
		$this->localCacheClass = $localCacheClass;
		$this->distributedCacheClass = $distributedCacheClass;
		$this->lockingCacheClass = $lockingCacheClass;
	}

	/**
	 * create a cache instance for storing locks
	 *
	 * @param string $prefix
	 * @return \OCP\IMemcache
	 */
	public function createLocking($prefix = '') {
		return new $this->lockingCacheClass($this->globalPrefix . '/' . $prefix);
	}

	/**
	 * create a distributed cache instance
	 *
	 * @param string $prefix
	 * @return \OC\Memcache\Cache
	 */
	public function createDistributed($prefix = '') {
		return new $this->distributedCacheClass($this->globalPrefix . '/' . $prefix);
	}

	/**
	 * create a local cache instance
	 *
	 * @param string $prefix
	 * @return \OC\Memcache\Cache
	 */
	public function createLocal($prefix = '') {
		return new $this->localCacheClass($this->globalPrefix . '/' . $prefix);
	}

	/**
	 * @see \OC\Memcache\Factory::createDistributed()
	 * @param string $prefix
	 * @return \OC\Memcache\Cache
	 */
	public function create($prefix = '') {
		return $this->createDistributed($prefix);
	}

	/**
	 * check memcache availability
	 *
	 * @return bool
	 */
	public function isAvailable() {
		return ($this->distributedCacheClass !== self::NULL_CACHE);
	}

	/**
	 * @see \OC\Memcache\Factory::createLocal()
	 * @param string $prefix
	 * @return \OC\Memcache\Cache|null
	 */
	public function createLowLatency($prefix = '') {
		return $this->createLocal($prefix);
	}

	/**
	 * check local memcache availability
	 *
	 * @return bool
	 */
	public function isAvailableLowLatency() {
		return ($this->localCacheClass !== self::NULL_CACHE);
	}
}
