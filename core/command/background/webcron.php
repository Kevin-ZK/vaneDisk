<?php

namespace OC\Core\Command\Background;

class WebCron extends Base {

	protected function getMode() {
		return 'webcron';
	}
}
