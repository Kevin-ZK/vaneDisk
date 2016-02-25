<?php

namespace OC;

class NaturalSort_DefaultCollator {
	public function compare($a, $b) {
		$result = strcasecmp($a, $b);
		if ($result === 0) {
			if ($a === $b) {
				return 0;
			}
			return ($a > $b) ? -1 : 1;
		}
		return ($result < 0) ? -1 : 1;
	}
}
