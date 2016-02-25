<?php

namespace OCP\Security;

/**
 * Class Hasher provides some basic hashing functions. Furthermore, it supports legacy hashes
 * used by previous versions of vanedisk and helps migrating those hashes to newer ones.
 *
 * The hashes generated by this class are prefixed (version|hash) with a version parameter to allow possible
 * updates in the future.
 * Possible versions:
 * 	- 1 (Initial version)
 *
 * Usage:
 * // Hashing a message
 * $hash = \OC::$server->getHasher()->hash('MessageToHash');
 * // Verifying a message - $newHash will contain the newly calculated hash
 * $newHash = null;
 * var_dump(\OC::$server->getHasher()->verify('a', '86f7e437faa5a7fce15d1ddcb9eaeaea377667b8', $newHash));
 * var_dump($newHash);
 *
 * @package OCP\Security
 * @since 8.0.0
 */
interface IHasher {
	/**
	 * Hashes a message using PHP's `password_hash` functionality.
	 * Please note that the size of the returned string is not guaranteed
	 * and can be up to 255 characters.
	 *
	 * @param string $message Message to generate hash from
	 * @return string Hash of the message with appended version parameter
	 * @since 8.0.0
	 */
	public function hash($message);

	/**
	 * @param string $message Message to verify
	 * @param string $hash Assumed hash of the message
	 * @param null|string &$newHash Reference will contain the updated hash if necessary. Update the existing hash with this one.
	 * @return bool Whether $hash is a valid hash of $message
	 * @since 8.0.0
	 */
	public function verify($message, $hash, &$newHash = null);
}
