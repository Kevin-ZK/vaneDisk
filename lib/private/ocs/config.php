<?php

class OC_OCS_Config {

	public static function apiConfig() {
		$xml['version'] = '1.7';
		$xml['website'] = 'ownCloud';
		$xml['host'] = OCP\Util::getServerHost();
		$xml['contact'] = '';
		$xml['ssl'] = 'false';
		return new OC_OCS_Result($xml);
	}

}
