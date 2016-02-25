<?php

namespace OCP\Encryption;

/**
 * Interface IFile
 *
 * @package OCP\Encryption
 * @since 8.1.0
 */
interface IFile {

	/**
	 * get list of users with access to the file
	 *
	 * @param string $path to the file
	 * @return array
	 * @since 8.1.0
	 */
	public function getAccessList($path);

}
