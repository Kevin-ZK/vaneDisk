<?php

namespace OCP\Encryption\Exceptions;
use OC\HintException;

/**
 * Class GenericEncryptionException
 *
 * @package OCP\Encryption\Exceptions
 * @since 8.1.0
 */
class GenericEncryptionException extends HintException {

	/**
	 * @param string $message
	 * @param string $hint
	 * @param int $code
	 * @param \Exception $previous
	 * @since 8.1.0
	 */
	public function __construct($message = '', $hint = '', $code = 0, \Exception $previous = null) {
		if (empty($message)) {
			$message = 'Unspecified encryption exception';
		}
		parent::__construct($message, $hint, $code, $previous);
	}

}
