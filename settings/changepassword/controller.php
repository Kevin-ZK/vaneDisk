<?php
namespace OC\Settings\ChangePassword;

class Controller {
	public static function changePersonalPassword($args) {
		// Check if we are an user
		\OC_JSON::callCheck();
		\OC_JSON::checkLoggedIn();

		$username = \OC_User::getUser();
		$password = isset($_POST['personal-password']) ? $_POST['personal-password'] : null;
		$oldPassword = isset($_POST['oldpassword']) ? $_POST['oldpassword'] : '';

		if (!\OC_User::checkPassword($username, $oldPassword)) {
			$l = new \OC_L10n('settings');
			\OC_JSON::error(array("data" => array("message" => $l->t("Wrong password")) ));
			exit();
		}
		if (!is_null($password) && \OC_User::setPassword($username, $password)) {
			\OC_JSON::success();
		} else {
			\OC_JSON::error();
		}
	}

	public static function changeUserPassword($args) {
		// Check if we are an user
		\OC_JSON::callCheck();
		\OC_JSON::checkLoggedIn();

		if (isset($_POST['username'])) {
			$username = $_POST['username'];
		} else {
			$l = new \OC_L10n('settings');
			\OC_JSON::error(array('data' => array('message' => $l->t('No user supplied')) ));
			exit();
		}

		$password = isset($_POST['password']) ? $_POST['password'] : null;
		$recoveryPassword = isset($_POST['recoveryPassword']) ? $_POST['recoveryPassword'] : null;

		if (\OC_User::isAdminUser(\OC_User::getUser())) {
			$userstatus = 'admin';
		} elseif (\OC_SubAdmin::isUserAccessible(\OC_User::getUser(), $username)) {
			$userstatus = 'subadmin';
		} else {
			$l = new \OC_L10n('settings');
			\OC_JSON::error(array('data' => array('message' => $l->t('Authentication error')) ));
			exit();
		}

		if (\OC_App::isEnabled('encryption')) {
			//handle the recovery case
			$crypt = new \OCA\Encryption\Crypto\Crypt(
				\OC::$server->getLogger(),
				\OC::$server->getUserSession(),
				\OC::$server->getConfig());
			$keyStorage = \OC::$server->getEncryptionKeyStorage();
			$util = new \OCA\Encryption\Util(
				new \OC\Files\View(),
				$crypt,
				\OC::$server->getLogger(),
				\OC::$server->getUserSession(),
				\OC::$server->getConfig(),
				\OC::$server->getUserManager());
			$keyManager = new \OCA\Encryption\KeyManager(
				$keyStorage,
				$crypt,
				\OC::$server->getConfig(),
				\OC::$server->getUserSession(),
				new \OCA\Encryption\Session(\OC::$server->getSession()),
				\OC::$server->getLogger(),
				$util);
			$recovery = new \OCA\Encryption\Recovery(
				\OC::$server->getUserSession(),
				$crypt,
				\OC::$server->getSecureRandom(),
				$keyManager,
				\OC::$server->getConfig(),
				$keyStorage,
				\OC::$server->getEncryptionFilesHelper(),
				new \OC\Files\View());
			$recoveryAdminEnabled = $recovery->isRecoveryKeyEnabled();

			$validRecoveryPassword = false;
			$recoveryEnabledForUser = false;
			if ($recoveryAdminEnabled) {
				$validRecoveryPassword = $keyManager->checkRecoveryPassword($recoveryPassword);
				$recoveryEnabledForUser = $recovery->isRecoveryEnabledForUser($username);
			}
			$l = new \OC_L10n('settings');

			if ($recoveryEnabledForUser && $recoveryPassword === '') {
				\OC_JSON::error(array('data' => array(
					'message' => $l->t('Please provide an admin recovery password, otherwise all user data will be lost')
				)));
			} elseif ($recoveryEnabledForUser && ! $validRecoveryPassword) {
				\OC_JSON::error(array('data' => array(
					'message' => $l->t('Wrong admin recovery password. Please check the password and try again.')
				)));
			} else { // now we know that everything is fine regarding the recovery password, let's try to change the password
				$result = \OC_User::setPassword($username, $password, $recoveryPassword);
				if (!$result && $recoveryEnabledForUser) {
					\OC_JSON::error(array(
						"data" => array(
							"message" => $l->t("Backend doesn't support password change, but the user's encryption key was successfully updated.")
						)
					));
				} elseif (!$result && !$recoveryEnabledForUser) {
					\OC_JSON::error(array("data" => array( "message" => $l->t("Unable to change password" ) )));
				} else {
					\OC_JSON::success(array("data" => array( "username" => $username )));
				}

			}
		} else { // if encryption is disabled, proceed
			if (!is_null($password) && \OC_User::setPassword($username, $password)) {
				\OC_JSON::success(array('data' => array('username' => $username)));
			} else {
				\OC_JSON::error(array('data' => array('message' => $l->t('Unable to change password'))));
			}
		}
	}
}
