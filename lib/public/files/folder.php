<?php
namespace OCP\Files;

/**
 * @since 6.0.0
 */
interface Folder extends Node {
	/**
	 * Get the full path of an item in the folder within owncloud's filesystem
	 *
	 * @param string $path relative path of an item in the folder
	 * @return string
	 * @throws \OCP\Files\NotPermittedException
	 * @since 6.0.0
	 */
	public function getFullPath($path);

	/**
	 * Get the path of an item in the folder relative to the folder
	 *
	 * @param string $path absolute path of an item in the folder
	 * @throws \OCP\Files\NotFoundException
	 * @return string
	 * @since 6.0.0
	 */
	public function getRelativePath($path);

	/**
	 * check if a node is a (grand-)child of the folder
	 *
	 * @param \OCP\Files\Node $node
	 * @return bool
	 * @since 6.0.0
	 */
	public function isSubNode($node);

	/**
	 * get the content of this directory
	 *
	 * @throws \OCP\Files\NotFoundException
	 * @return \OCP\Files\Node[]
	 * @since 6.0.0
	 */
	public function getDirectoryListing();

	/**
	 * Get the node at $path
	 *
	 * @param string $path relative path of the file or folder
	 * @return \OCP\Files\Node
	 * @throws \OCP\Files\NotFoundException
	 * @since 6.0.0
	 */
	public function get($path);

	/**
	 * Check if a file or folder exists in the folder
	 *
	 * @param string $path relative path of the file or folder
	 * @return bool
	 * @since 6.0.0
	 */
	public function nodeExists($path);

	/**
	 * Create a new folder
	 *
	 * @param string $path relative path of the new folder
	 * @return \OCP\Files\Folder
	 * @throws \OCP\Files\NotPermittedException
	 * @since 6.0.0
	 */
	public function newFolder($path);

	/**
	 * Create a new file
	 *
	 * @param string $path relative path of the new file
	 * @return \OCP\Files\File
	 * @throws \OCP\Files\NotPermittedException
	 * @since 6.0.0
	 */
	public function newFile($path);

	/**
	 * search for files with the name matching $query
	 *
	 * @param string $query
	 * @return \OCP\Files\Node[]
	 * @since 6.0.0
	 */
	public function search($query);

	/**
	 * search for files by mimetype
	 * $mimetype can either be a full mimetype (image/png) or a wildcard mimetype (image)
	 *
	 * @param string $mimetype
	 * @return \OCP\Files\Node[]
	 * @since 6.0.0
	 */
	public function searchByMime($mimetype);

	/**
	 * search for files by tag
	 *
	 * @param string|int $tag tag name or tag id
	 * @param string $userId owner of the tags
	 * @return \OCP\Files\Node[]
	 * @since 8.0.0
	 */
	public function searchByTag($tag, $userId);

	/**
	 * get a file or folder inside the folder by it's internal id
	 *
	 * @param int $id
	 * @return \OCP\Files\Node[]
	 * @since 6.0.0
	 */
	public function getById($id);

	/**
	 * Get the amount of free space inside the folder
	 *
	 * @return int
	 * @since 6.0.0
	 */
	public function getFreeSpace();

	/**
	 * Check if new files or folders can be created within the folder
	 *
	 * @return bool
	 * @since 6.0.0
	 */
	public function isCreatable();

	/**
	 * Add a suffix to the name in case the file exists
	 *
	 * @param string $name
	 * @return string
	 * @throws NotPermittedException
	 * @since 8.1.0
	 */
	public function getNonExistingName($name);
}
