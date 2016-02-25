<?php

namespace OC\Memcache;

trait CASTrait {
	abstract public function get($key);

	abstract public function set($key, $value, $ttl = 0);

	abstract public function remove($key);

	abstract public function add($key, $value, $ttl = 0);

	/**
	 * Compare and set
	 *
	 * @param string $key
	 * @param mixed $old
	 * @param mixed $new
	 * @return bool
	 */
	public function cas($key, $old, $new) {
		//no native cas, emulate with locking
		if ($this->add($key . '_lock', true)) {
			if ($this->get($key) === $old) {
				$this->set($key, $new);
				$this->remove($key . '_lock');
				return true;
			} else {
				$this->remove($key . '_lock');
				return false;
			}
		} else {
			return false;
		}
	}
}
