<?php

namespace OC\Memcache;

abstract class Cache implements \ArrayAccess, \OCP\ICache {
	/**
	 * @var string $prefix
	 */
	protected $prefix;

	/**
	 * @param string $prefix
	 */
	public function __construct($prefix = '') {
		$this->prefix = $prefix;
	}

	/**
	 * @return string Prefix used for caching purposes
	 */
	public function getPrefix() {
		return $this->prefix;
	}

	/**
	 * @param string $key
	 * @return mixed
	 */
	abstract public function get($key);

	/**
	 * @param string $key
	 * @param mixed $value
	 * @param int $ttl
	 * @return mixed
	 */
	abstract public function set($key, $value, $ttl = 0);

	/**
	 * @param string $key
	 * @return mixed
	 */
	abstract public function hasKey($key);

	/**
	 * @param string $key
	 * @return mixed
	 */
	abstract public function remove($key);

	/**
	 * @param string $prefix
	 * @return mixed
	 */
	abstract public function clear($prefix = '');

	//implement the ArrayAccess interface

	public function offsetExists($offset) {
		return $this->hasKey($offset);
	}

	public function offsetSet($offset, $value) {
		$this->set($offset, $value);
	}

	public function offsetGet($offset) {
		return $this->get($offset);
	}

	public function offsetUnset($offset) {
		$this->remove($offset);
	}
}
