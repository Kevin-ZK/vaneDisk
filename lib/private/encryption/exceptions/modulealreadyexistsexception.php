<?php

namespace OC\Encryption\Exceptions;

use OCP\Encryption\Exceptions\GenericEncryptionException;

class ModuleAlreadyExistsException extends GenericEncryptionException {

	/**
	 * @param string $id
	 * @param string $name
	 */
	public function __construct($id, $name) {
		parent::__construct('Id "' . $id . '" already used by encryption module "' . $name . '"');
	}

}
