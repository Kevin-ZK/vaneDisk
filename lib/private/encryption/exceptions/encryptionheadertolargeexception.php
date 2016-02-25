<?php


namespace OC\Encryption\Exceptions;

use OCP\Encryption\Exceptions\GenericEncryptionException;

class EncryptionHeaderToLargeException extends GenericEncryptionException {

	public function __construct() {
		parent::__construct('max header size exceeded');
	}

}
