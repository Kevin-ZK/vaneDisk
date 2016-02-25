<?php

namespace OC\Encryption\Exceptions;

use OCP\Encryption\Exceptions\GenericEncryptionException;

class EncryptionHeaderKeyExistsException extends GenericEncryptionException {

	/**
	 * @param string $key
	 */
	public function __construct($key) {
		parent::__construct('header key "'. $key . '" already reserved by ownCloud');
	}
}
