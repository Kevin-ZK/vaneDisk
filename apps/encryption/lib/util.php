<?php

namespace OCA\Encryption;


use OC\Files\View;
use OCA\Encryption\Crypto\Crypt;
use OCP\IConfig;
use OCP\ILogger;
use OCP\IUser;
use OCP\IUserManager;
use OCP\IUserSession;
use OCP\PreConditionNotMetException;

class Util {
	/**
	 * @var View
	 */
	private $files;
	/**
	 * @var Crypt
	 */
	private $crypt;
	/**
	 * @var ILogger
	 */
	private $logger;
	/**
	 * @var bool|IUser
	 */
	private $user;
	/**
	 * @var IConfig
	 */
	private $config;
	/**
	 * @var IUserManager
	 */
	private $userManager;

	/**
	 * Util constructor.
	 *
	 * @param View $files
	 * @param Crypt $crypt
	 * @param ILogger $logger
	 * @param IUserSession $userSession
	 * @param IConfig $config
	 * @param IUserManager $userManager
	 */
	public function __construct(View $files,
								Crypt $crypt,
								ILogger $logger,
								IUserSession $userSession,
								IConfig $config,
								IUserManager $userManager
	) {
		$this->files = $files;
		$this->crypt = $crypt;
		$this->logger = $logger;
		$this->user = $userSession && $userSession->isLoggedIn() ? $userSession->getUser() : false;
		$this->config = $config;
		$this->userManager = $userManager;
	}

	/**
	 * check if recovery key is enabled for user
	 *
	 * @param string $uid
	 * @return bool
	 */
	public function isRecoveryEnabledForUser($uid) {
		$recoveryMode = $this->config->getUserValue($uid,
			'encryption',
			'recoveryEnabled',
			0);

		return ($recoveryMode === '1');
	}

	/**
	 * @param $enabled
	 * @return bool
	 */
	public function setRecoveryForUser($enabled) {
		$value = $enabled ? '1' : '0';

		try {
			$this->config->setUserValue($this->user->getUID(),
				'encryption',
				'recoveryEnabled',
				$value);
			return true;
		} catch (PreConditionNotMetException $e) {
			return false;
		}
	}

	/**
	 * @param string $uid
	 * @return bool
	 */
	public function userHasFiles($uid) {
		return $this->files->file_exists($uid . '/files');
	}

	/**
	 * get owner from give path, path relative to data/ expected
	 *
	 * @param string $path relative to data/
	 * @return string
	 * @throws \BadMethodCallException
	 */
	public function getOwner($path) {
		$owner = '';
		$parts = explode('/', $path, 3);
		if (count($parts) > 1) {
			$owner = $parts[1];
			if ($this->userManager->userExists($owner) === false) {
				throw new \BadMethodCallException('Unknown user: ' .
				'method expects path to a user folder relative to the data folder');
			}

		}

		return $owner;
	}

}
