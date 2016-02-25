<?php

namespace OC\Memcache;

use OCP\IMemcache;

class APC extends Cache implements IMemcache {
	use CASTrait {
		cas as casEmulated;
	}

	use CADTrait;

	public function get($key) {
		$result = apc_fetch($this->getPrefix() . $key, $success);
		if (!$success) {
			return null;
		}
		return $result;
	}

	public function set($key, $value, $ttl = 0) {
		return apc_store($this->getPrefix() . $key, $value, $ttl);
	}

	public function hasKey($key) {
		return apc_exists($this->getPrefix() . $key);
	}

	public function remove($key) {
		return apc_delete($this->getPrefix() . $key);
	}

	public function clear($prefix = '') {
		$ns = $this->getPrefix() . $prefix;
		$ns = preg_quote($ns, '/');
		$iter = new \APCIterator('user', '/^' . $ns . '/', APC_ITER_KEY);
		return apc_delete($iter);
	}

	/**
	 * Set a value in the cache if it's not already stored
	 *
	 * @param string $key
	 * @param mixed $value
	 * @param int $ttl Time To Live in seconds. Defaults to 60*60*24
	 * @return bool
	 */
	public function add($key, $value, $ttl = 0) {
		return apc_add($this->getPrefix() . $key, $value, $ttl);
	}

	/**
	 * Increase a stored number
	 *
	 * @param string $key
	 * @param int $step
	 * @return int | bool
	 */
	public function inc($key, $step = 1) {
		$this->add($key, 0);
		return apc_inc($this->getPrefix() . $key, $step);
	}

	/**
	 * Decrease a stored number
	 *
	 * @param string $key
	 * @param int $step
	 * @return int | bool
	 */
	public function dec($key, $step = 1) {
		return apc_dec($this->getPrefix() . $key, $step);
	}

	/**
	 * Compare and set
	 *
	 * @param string $key
	 * @param mixed $old
	 * @param mixed $new
	 * @return bool
	 */
	public function cas($key, $old, $new) {
		// apc only does cas for ints
		if (is_int($old) and is_int($new)) {
			return apc_cas($this->getPrefix() . $key, $old, $new);
		} else {
			return $this->casEmulated($key, $old, $new);
		}
	}

	static public function isAvailable() {
		if (!extension_loaded('apc')) {
			return false;
		} elseif (!ini_get('apc.enabled')) {
			return false;
		} elseif (!ini_get('apc.enable_cli') && \OC::$CLI) {
			return false;
		} else {
			return true;
		}
	}
}

if (!function_exists('apc_exists')) {
	function apc_exists($keys) {
		$result = false;
		apc_fetch($keys, $result);
		return $result;
	}
}
