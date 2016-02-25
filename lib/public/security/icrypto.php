<?php

namespace OCP\Security;

/**
 * Class Crypto provides a high-level encryption layer using AES-CBC. If no key has been provided
 * it will use the secret defined in config.php as key. Additionally the message will be HMAC'd.
 *
 * Usage:
 * $encryptWithDefaultPassword = \OC::$server->getCrypto()->encrypt('EncryptedText');
 * $encryptWithCustomPassword = \OC::$server->getCrypto()->encrypt('EncryptedText', 'password');
 *
 * @package OCP\Security
 * @since 8.0.0
 */
interface ICrypto {

	/**
	 * @param string $message The message to authenticate
	 * @param string $password Password to use (defaults to `secret` in config.php)
	 * @return string Calculated HMAC
	 * @since 8.0.0
	 */
	public function calculateHMAC($message, $password = '');

	/**
	 * Encrypts a value and adds an HMAC (Encrypt-Then-MAC)
	 * @param string $plaintext
	 * @param string $password Password to encrypt, if not specified the secret from config.php will be taken
	 * @return string Authenticated ciphertext
	 * @since 8.0.0
	 */
	public function encrypt($plaintext, $password = '');

	/**
	 * Decrypts a value and verifies the HMAC (Encrypt-Then-Mac)
	 * @param string $authenticatedCiphertext
	 * @param string $password Password to encrypt, if not specified the secret from config.php will be taken
	 * @return string plaintext
	 * @throws \Exception If the HMAC does not match
	 * @since 8.0.0
	 */
	public function decrypt($authenticatedCiphertext, $password = '');
}
