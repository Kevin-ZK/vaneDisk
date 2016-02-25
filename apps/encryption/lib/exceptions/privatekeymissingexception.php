<?php

namespace OCA\Encryption\Exceptions;

use OCP\Encryption\Exceptions\GenericEncryptionException;

class PrivateKeyMissingException extends GenericEncryptionException {

	/**
	 * @param string $userId
	 */
	public function __construct($userId) {
		if(empty($userId)) {
			$userId = "<no-user-id-given>";
		}
		parent::__construct("Private Key missing for user: $userId");
	}

}
