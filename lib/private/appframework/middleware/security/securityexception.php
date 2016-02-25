<?php

namespace OC\AppFramework\Middleware\Security;


/**
 * Thrown when the security middleware encounters a security problem
 */
class SecurityException extends \Exception {

	/**
	 * @param string $msg the security error message
	 */
	public function __construct($msg, $code = 0) {
		parent::__construct($msg, $code);
	}

}
