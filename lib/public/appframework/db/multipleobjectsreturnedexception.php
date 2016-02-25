<?php

namespace OCP\AppFramework\Db;


/**
 * This is returned or should be returned when a find request finds more than one
 * row
 * @since 7.0.0
 */
class MultipleObjectsReturnedException extends \Exception {

	/**
	 * Constructor
	 * @param string $msg the error message
	 * @since 7.0.0
	 */
	public function __construct($msg){
		parent::__construct($msg);
	}

}
