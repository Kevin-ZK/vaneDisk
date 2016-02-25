<?php

namespace OCP;

/**
 * Interface ITempManager
 *
 * @package OCP
 * @since 8.0.0
 */
interface ITempManager {
	/**
	 * Create a temporary file and return the path
	 *
	 * @param string $postFix
	 * @return string
	 * @since 8.0.0
	 */
	public function getTemporaryFile($postFix = '');

	/**
	 * Create a temporary folder and return the path
	 *
	 * @param string $postFix
	 * @return string
	 * @since 8.0.0
	 */
	public function getTemporaryFolder($postFix = '');

	/**
	 * Remove the temporary files and folders generated during this request
	 * @since 8.0.0
	 */
	public function clean();

	/**
	 * Remove old temporary files and folders that were failed to be cleaned
	 * @since 8.0.0
	 */
	public function cleanOld();
}
