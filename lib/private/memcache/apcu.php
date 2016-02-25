<?php

namespace OC\Memcache;

class APCu extends APC {
	static public function isAvailable() {
		if (!extension_loaded('apcu')) {
			return false;
		} elseif (!ini_get('apc.enabled')) {
			return false;
		} elseif (!ini_get('apc.enable_cli') && \OC::$CLI) {
			return false;
		} elseif (version_compare(phpversion('apc'), '4.0.6') === -1) {
			return false;
		} else {
			return true;
		}
	}
}
