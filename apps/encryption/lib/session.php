<?php

namespace OCA\Encryption;

use \OCP\ISession;

class Session {

	/** @var ISession */
	protected $session;

	const NOT_INITIALIZED = '0';
	const INIT_EXECUTED = '1';
	const INIT_SUCCESSFUL = '2';
	const RUN_MIGRATION = '3';

	/**
	 * @param ISession $session
	 */
	public function __construct(ISession $session) {
		$this->session = $session;
	}

	/**
	 * Sets status of encryption app
	 *
	 * @param string $status INIT_SUCCESSFUL, INIT_EXECUTED, NOT_INITIALIZED
	 */
	public function setStatus($status) {
		$this->session->set('encryptionInitialized', $status);
	}

	/**
	 * Gets status if we already tried to initialize the encryption app
	 *
	 * @return string init status INIT_SUCCESSFUL, INIT_EXECUTED, NOT_INITIALIZED
	 */
	public function getStatus() {
		$status = $this->session->get('encryptionInitialized');
		if (is_null($status)) {
			$status = self::NOT_INITIALIZED;
		}

		return $status;
	}

	/**
	 * Gets user or public share private key from session
	 *
	 * @return string $privateKey The user's plaintext private key
	 * @throws Exceptions\PrivateKeyMissingException
	 */
	public function getPrivateKey() {
		$key = $this->session->get('privateKey');
		if (is_null($key)) {
			throw new Exceptions\PrivateKeyMissingException('please try to log-out and log-in again', 0);
		}
		return $key;
	}

	/**
	 * check if private key is set
	 *
	 * @return boolean
	 */
	public function isPrivateKeySet() {
		$key = $this->session->get('privateKey');
		if (is_null($key)) {
			return false;
		}

		return true;
	}

	/**
	 * Sets user private key to session
	 *
	 * @param string $key users private key
	 *
	 * @note this should only be set on login
	 */
	public function setPrivateKey($key) {
		$this->session->set('privateKey', $key);
	}


	/**
	 * remove keys from session
	 */
	public function clear() {
		$this->session->remove('publicSharePrivateKey');
		$this->session->remove('privateKey');
		$this->session->remove('encryptionInitialized');

	}

}
