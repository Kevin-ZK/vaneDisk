<?php

namespace OCA\Encryption\Users;


use OCA\Encryption\Crypto\Crypt;
use OCA\Encryption\KeyManager;
use OCP\ILogger;
use OCP\IUserSession;

class Setup {
	/**
	 * @var Crypt
	 */
	private $crypt;
	/**
	 * @var KeyManager
	 */
	private $keyManager;
	/**
	 * @var ILogger
	 */
	private $logger;
	/**
	 * @var bool|string
	 */
	private $user;


	/**
	 * @param ILogger $logger
	 * @param IUserSession $userSession
	 * @param Crypt $crypt
	 * @param KeyManager $keyManager
	 */
	public function __construct(ILogger $logger,
								IUserSession $userSession,
								Crypt $crypt,
								KeyManager $keyManager) {
		$this->logger = $logger;
		$this->user = $userSession && $userSession->isLoggedIn() ? $userSession->getUser()->getUID() : false;
		$this->crypt = $crypt;
		$this->keyManager = $keyManager;
 	}

	/**
	 * @param string $uid userid
	 * @param string $password user password
	 * @return bool
	 */
	public function setupUser($uid, $password) {
		return $this->setupServerSide($uid, $password);
	}

	/**
	 * @param string $uid userid
	 * @param string $password user password
	 * @return bool
	 */
	public function setupServerSide($uid, $password) {
		$this->keyManager->validateShareKey();
		// Check if user already has keys
		if (!$this->keyManager->userHasKeys($uid)) {
			return $this->keyManager->storeKeyPair($uid, $password,
				$this->crypt->createKeyPair());
		}
		return true;
	}
}
