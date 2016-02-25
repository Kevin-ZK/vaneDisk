<?php
namespace OCP;

/**
 * This class provides access to the internal filesystem abstraction layer. Use
 * this class exlusively if you want to access files
 * @since 5.0.0
 */
class Files {
	/**
	 * Recusive deletion of folders
	 * @return bool
	 * @since 5.0.0
	 */
	static function rmdirr( $dir ) {
		return \OC_Helper::rmdirr( $dir );
	}

	/**
	 * Get the mimetype form a local file
	 * @param string $path
	 * @return string
	 * does NOT work for ownClouds filesystem, use OC_FileSystem::getMimeType instead
	 * @since 5.0.0
	 */
	static function getMimeType( $path ) {
		return(\OC_Helper::getMimeType( $path ));
	}

	/**
	 * Search for files by mimetype
	 * @param string $mimetype
	 * @return array
	 * @since 6.0.0
	 */
	static public function searchByMime( $mimetype ) {
		return(\OC\Files\Filesystem::searchByMime( $mimetype ));
	}

	/**
	 * Copy the contents of one stream to another
	 * @param resource $source
	 * @param resource $target
	 * @return int the number of bytes copied
	 * @since 5.0.0
	 */
	public static function streamCopy( $source, $target ) {
		list($count, ) = \OC_Helper::streamCopy( $source, $target );
		return $count;
	}

	/**
	 * Create a temporary file with an unique filename
	 * @param string $postfix
	 * @return string
	 *
	 * temporary files are automatically cleaned up after the script is finished
	 * @deprecated 8.1.0 use getTemporaryFile() of \OCP\ITempManager - \OC::$server->getTempManager()
	 * @since 5.0.0
	 */
	public static function tmpFile( $postfix='' ) {
		return \OC::$server->getTempManager()->getTemporaryFile($postfix);
	}

	/**
	 * Create a temporary folder with an unique filename
	 * @return string
	 *
	 * temporary files are automatically cleaned up after the script is finished
	 * @deprecated 8.1.0 use getTemporaryFolder() of \OCP\ITempManager - \OC::$server->getTempManager()
	 * @since 5.0.0
	 */
	public static function tmpFolder() {
		return \OC::$server->getTempManager()->getTemporaryFolder();
	}

	/**
	 * Adds a suffix to the name in case the file exists
	 * @param string $path
	 * @param string $filename
	 * @return string
	 * @since 5.0.0
	 */
	public static function buildNotExistingFileName( $path, $filename ) {
		return(\OC_Helper::buildNotExistingFileName( $path, $filename ));
	}

	/**
	 * Gets the Storage for an app - creates the needed folder if they are not
	 * existant
	 * @param string $app
	 * @return \OC\Files\View
	 * @since 5.0.0
	 */
	public static function getStorage( $app ) {
		return \OC_App::getStorage( $app );
	}
}
