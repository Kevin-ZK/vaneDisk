<?php

namespace OC\Search\Result;
use OCP\Files\FileInfo;
use OCP\Files\Folder;

/**
 * A found file
 */
class File extends \OCP\Search\Result {

	/**
	 * Type name; translated in templates
	 * @var string 
	 */
	public $type = 'file';

	/**
	 * Path to file
	 * @var string
	 */
	public $path;

	/**
	 * Size, in bytes
	 * @var int 
	 */
	public $size;

	/**
	 * Date modified, in human readable form
	 * @var string
	 */
	public $modified;

	/**
	 * File mime type
	 * @var string
	 */
	public $mime_type;

	/**
	 * File permissions:
	 * 
	 * @var string
	 */
	public $permissions;

	/**
	 * Create a new file search result
	 * @param FileInfo $data file data given by provider
	 */
	public function __construct(FileInfo $data) {

		$path = $this->getRelativePath($data->getPath());

		$info = pathinfo($path);
		$this->id = $data->getId();
		$this->name = $info['basename'];
		$this->link = \OCP\Util::linkTo(
			'files',
			'index.php',
			array('dir' => $info['dirname'], 'scrollto' => $info['basename'])
		);
		$this->permissions = $data->getPermissions();
		$this->path = $path;
		$this->size = $data->getSize();
		$this->modified = $data->getMtime();
		$this->mime = $data->getMimetype();
	}

	/**
	 * @var Folder $userFolderCache
	 */
	static protected $userFolderCache = null;

	/**
	 * converts a path relative to the users files folder
	 * eg /user/files/foo.txt -> /foo.txt
	 * @param string $path
	 * @return string relative path
	 */
	protected function getRelativePath ($path) {
		if (!isset(self::$userFolderCache)) {
			$user = \OC::$server->getUserSession()->getUser()->getUID();
			self::$userFolderCache = \OC::$server->getUserFolder($user);
		}
		return self::$userFolderCache->getRelativePath($path);
	}

}
