<?php

namespace OCA\Encryption\Hooks;


use OCP\Util as OCUtil;
use OCA\Encryption\Hooks\Contracts\IHook;
use OCA\Encryption\KeyManager;
use OCA\Encryption\Crypto\Crypt;
use OCA\Encryption\Users\Setup;
use OCP\App;
use OCP\ILogger;
use OCP\IUserSession;
use OCA\Encryption\Util;
use OCA\Encryption\Session;
use OCA\Encryption\Recovery;

class UserHooks implements IHook {
	/**
	 * @var KeyManager
	 */
	private $keyManager;
	/**
	 * @var ILogger
	 */
	private $logger;
	/**
	 * @var Setup
	 */
	private $userSetup;
	/**
	 * @var IUserSession
	 */
	private $user;
	/**
	 * @var Util
	 */
	private $util;
	/**
	 * @var Session
	 */
	private $session;
	/**
	 * @var Recovery
	 */
	private $recovery;
	/**
	 * @var Crypt
	 */
	private $crypt;

	/**
	 * UserHooks constructor.
	 *
	 * @param KeyManager $keyManager
	 * @param ILogger $logger
	 * @param Setup $userSetup
	 * @param IUserSession $user
	 * @param Util $util
	 * @param Session $session
	 * @param Crypt $crypt
	 * @param Recovery $recovery
	 */
	public function __construct(KeyManager $keyManager,
								ILogger $logger,
								Setup $userSetup,
								IUserSession $user,
								Util $util,
								Session $session,
								Crypt $crypt,
								Recovery $recovery) {

		$this->keyManager = $keyManager;
		$this->logger = $logger;
		$this->userSetup = $userSetup;
		$this->user = $user;
		$this->util = $util;
		$this->session = $session;
		$this->recovery = $recovery;
		$this->crypt = $crypt;
	}

	/**
	 * Connects Hooks
	 *
	 * @return null
	 */
	public function addHooks() {
		OCUtil::connectHook('OC_User', 'post_login', $this, 'login');
		OCUtil::connectHook('OC_User', 'logout', $this, 'logout');
		OCUtil::connectHook('OC_User',
			'post_setPassword',
			$this,
			'setPassphrase');
		OCUtil::connectHook('OC_User',
			'pre_setPassword',
			$this,
			'preSetPassphrase');
		OCUtil::connectHook('OC_User',
			'post_createUser',
			$this,
			'postCreateUser');
		OCUtil::connectHook('OC_User',
			'post_deleteUser',
			$this,
			'postDeleteUser');
	}


	/**
	 * Startup encryption backend upon user login
	 *
	 * @note This method should never be called for users using client side encryption
	 * @param array $params
	 * @return bool
	 */
	public function login($params) {

		if (!App::isEnabled('encryption')) {
			return true;
		}

		// ensure filesystem is loaded
		if (!\OC\Files\Filesystem::$loaded) {
			\OC_Util::setupFS($params['uid']);
		}

		// setup user, if user not ready force relogin
		if (!$this->userSetup->setupUser($params['uid'], $params['password'])) {
			return false;
		}

		$this->keyManager->init($params['uid'], $params['password']);
	}

	/**
	 * remove keys from session during logout
	 */
	public function logout() {
		$this->session->clear();
	}

	/**
	 * setup encryption backend upon user created
	 *
	 * @note This method should never be called for users using client side encryption
	 * @param array $params
	 */
	public function postCreateUser($params) {

		if (App::isEnabled('encryption')) {
			$this->userSetup->setupUser($params['uid'], $params['password']);
		}
	}

	/**
	 * cleanup encryption backend upon user deleted
	 *
	 * @param array $params : uid, password
	 * @note This method should never be called for users using client side encryption
	 */
	public function postDeleteUser($params) {

		if (App::isEnabled('encryption')) {
			$this->keyManager->deletePublicKey($params['uid']);
		}
	}

	/**
	 * If the password can't be changed within ownCloud, than update the key password in advance.
	 *
	 * @param array $params : uid, password
	 * @return bool
	 */
	public function preSetPassphrase($params) {
		if (App::isEnabled('encryption')) {

			$user = $this->user->getUser();

			if ($user && !$user->canChangePassword()) {
				$this->setPassphrase($params);
			}
		}
	}

	/**
	 * Change a user's encryption passphrase
	 *
	 * @param array $params keys: uid, password
	 * @return bool
	 */
	public function setPassphrase($params) {

		// Get existing decrypted private key
		$privateKey = $this->session->getPrivateKey();
		$user = $this->user->getUser();

		// current logged in user changes his own password
		if ($user && $params['uid'] === $user->getUID() && $privateKey) {

			// Encrypt private key with new user pwd as passphrase
			$encryptedPrivateKey = $this->crypt->symmetricEncryptFileContent($privateKey,
				$params['password']);

			// Save private key
			if ($encryptedPrivateKey) {
				$this->keyManager->setPrivateKey($this->user->getUser()->getUID(),
					$this->crypt->generateHeader() . $encryptedPrivateKey);
			} else {
				$this->logger->error('Encryption could not update users encryption password');
			}

			// NOTE: Session does not need to be updated as the
			// private key has not changed, only the passphrase
			// used to decrypt it has changed
		} else { // admin changed the password for a different user, create new keys and re-encrypt file keys
			$user = $params['uid'];
			$recoveryPassword = isset($params['recoveryPassword']) ? $params['recoveryPassword'] : null;

			// we generate new keys if...
			// ...we have a recovery password and the user enabled the recovery key
			// ...encryption was activated for the first time (no keys exists)
			// ...the user doesn't have any files
			if (
				($this->recovery->isRecoveryEnabledForUser($user) && $recoveryPassword)
				|| !$this->keyManager->userHasKeys($user)
				|| !$this->util->userHasFiles($user)
			) {

				// backup old keys
				//$this->backupAllKeys('recovery');

				$newUserPassword = $params['password'];

				$keyPair = $this->crypt->createKeyPair();

				// Save public key
				$this->keyManager->setPublicKey($user, $keyPair['publicKey']);

				// Encrypt private key with new password
				$encryptedKey = $this->crypt->symmetricEncryptFileContent($keyPair['privateKey'],
					$newUserPassword);

				if ($encryptedKey) {
					$this->keyManager->setPrivateKey($user, $this->crypt->generateHeader() . $encryptedKey);

					if ($recoveryPassword) { // if recovery key is set we can re-encrypt the key files
						$this->recovery->recoverUsersFiles($recoveryPassword, $user);
					}
				} else {
					$this->logger->error('Encryption Could not update users encryption password');
				}
			}
		}
	}



	/**
	 * after password reset we create a new key pair for the user
	 *
	 * @param array $params
	 */
	public function postPasswordReset($params) {
		$password = $params['password'];

		$this->keyManager->replaceUserKeys($params['uid']);
		$this->userSetup->setupServerSide($params['uid'], $password);
	}
}
