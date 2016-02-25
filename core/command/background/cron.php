<?php

namespace OC\Core\Command\Background;

class Cron extends Base {

	protected function getMode() {
		return 'cron';
	}
}
