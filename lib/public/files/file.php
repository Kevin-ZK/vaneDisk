<?php
namespace OCP\Files;

/**
 * Interface File
 *
 * @package OCP\Files
 * @since 6.0.0
 */
interface File extends Node {
	/**
	 * Get the content of the file as string
	 *
	 * @return string
	 * @throws \OCP\Files\NotPermittedException
	 * @since 6.0.0
	 */
	public function getContent();

	/**
	 * Write to the file from string data
	 *
	 * @param string $data
	 * @throws \OCP\Files\NotPermittedException
	 * @return void
	 * @since 6.0.0
	 */
	public function putContent($data);

	/**
	 * Get the mimetype of the file
	 *
	 * @return string
	 * @since 6.0.0
	 */
	public function getMimeType();

	/**
	 * Open the file as stream, resulting resource can be operated as stream like the result from php's own fopen
	 *
	 * @param string $mode
	 * @return resource
	 * @throws \OCP\Files\NotPermittedException
	 * @since 6.0.0
	 */
	public function fopen($mode);

	/**
	 * Compute the hash of the file
	 * Type of hash is set with $type and can be anything supported by php's hash_file
	 *
	 * @param string $type
	 * @param bool $raw
	 * @return string
	 * @since 6.0.0
	 */
	public function hash($type, $raw = false);
}
