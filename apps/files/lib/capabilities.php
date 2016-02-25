<?php
 
namespace OCA\Files; 

class Capabilities {
	
	public static function getCapabilities() {
		return new \OC_OCS_Result(array(
			'capabilities' => array(
				'files' => array(
					'bigfilechunking' => true,
					),
				),
			));
	}
	
}
