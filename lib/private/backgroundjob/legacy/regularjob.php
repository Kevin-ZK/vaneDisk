<?php

namespace OC\BackgroundJob\Legacy;

class RegularJob extends \OC\BackgroundJob\Job {
	public function run($argument) {
		if (is_callable($argument)) {
			call_user_func($argument);
		}
	}
}
