<?php
 
namespace OCA\Files_Trashbin;


/**
 * Class Capabilities
 *
 * @package OCA\Files_Trashbin
 */
class Capabilities {

	/**
	 * @return \OC_OCS_Result
	 */
	public static function getCapabilities() {
		return new \OC_OCS_Result(array(
			'capabilities' => array(
				'files' => array(
					'undelete' => true,
					),
				),
			));
	}
	
}
