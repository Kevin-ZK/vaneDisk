<?php
 
namespace OCA\Files_Versions; 

class Capabilities {
	
	public static function getCapabilities() {
		return new \OC_OCS_Result(array(
			'capabilities' => array(
				'files' => array(
					'versioning' => true,
					),
				),
			));
	}
	
}
