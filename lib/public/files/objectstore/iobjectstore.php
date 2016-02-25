<?php
namespace OCP\Files\ObjectStore;

/**
 * Interface IObjectStore
 *
 * @package OCP\Files\ObjectStore
 * @since 7.0.0
 */
interface IObjectStore {

	/**
	 * @return string the container or bucket name where objects are stored
	 * @since 7.0.0
	 */
	function getStorageId();

	/**
	 * @param string $urn the unified resource name used to identify the object
	 * @return resource stream with the read data
	 * @throws \Exception when something goes wrong, message will be logged
	 * @since 7.0.0
	 */
	function readObject($urn);

	/**
	 * @param string $urn the unified resource name used to identify the object
	 * @param resource $stream stream with the data to write
	 * @throws \Exception when something goes wrong, message will be logged
	 * @since 7.0.0
	 */
	function writeObject($urn, $stream);

	/**
	 * @param string $urn the unified resource name used to identify the object
	 * @return void
	 * @throws \Exception when something goes wrong, message will be logged
	 * @since 7.0.0
	 */
	 function deleteObject($urn);

}
