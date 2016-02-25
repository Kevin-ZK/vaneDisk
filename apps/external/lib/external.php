<?php

namespace OCA\External;

class External {

	public static function getSites() {
		if (($sites = json_decode(\OCP\Config::getAppValue("external", "sites", ''))) != null) {
			return $sites;
		}

		return array();
	}

}
