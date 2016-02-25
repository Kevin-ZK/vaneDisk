<?php

namespace OC\Memcache;

trait CADTrait {
	abstract public function get($key);

	abstract public function remove($key);

	abstract public function add($key, $value, $ttl = 0);

	/**
	 * Compare and delete
	 *
	 * @param string $key
	 * @param mixed $old
	 * @return bool
	 */
	public function cad($key, $old) {
		//no native cas, emulate with locking
		if ($this->add($key . '_lock', true)) {
			if ($this->get($key) === $old) {
				$this->remove($key);
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
