<?php

namespace OCP\Security;

/**
 * Class StringUtils
 *
 * @package OCP\Security
 * @since 8.0.0
 */
class StringUtils {
	/**
	 * Compares whether two strings are equal. To prevent guessing of the string
	 * length this is done by comparing two hashes against each other and afterwards
	 * a comparison of the real string to prevent against the unlikely chance of
	 * collisions.
	 * @param string $expected The expected value
	 * @param string $input The input to compare against
	 * @return bool True if the two strings are equal, otherwise false.
	 * @since 8.0.0
	 */
	public static function equals($expected, $input) {
		return \OC\Security\StringUtils::equals($expected, $input);
	}
}
