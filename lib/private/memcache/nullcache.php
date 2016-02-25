<?php

namespace OC\Memcache;

class NullCache extends Cache implements \OCP\IMemcache {
	public function get($key) {
		return null;
	}

	public function set($key, $value, $ttl = 0) {
		return true;
	}

	public function hasKey($key) {
		return false;
	}

	public function remove($key) {
		return true;
	}

	public function add($key, $value, $ttl = 0) {
		return true;
	}

	public function inc($key, $step = 1) {
		return true;
	}

	public function dec($key, $step = 1) {
		return true;
	}

	public function cas($key, $old, $new) {
		return true;
	}

	public function cad($key, $old) {
		return true;
	}

	public function clear($prefix = '') {
		return true;
	}

	static public function isAvailable() {
		return true;
	}
}
