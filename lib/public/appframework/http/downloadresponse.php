<?php


namespace OCP\AppFramework\Http;


/**
 * Prompts the user to download the a file
 * @since 7.0.0
 */
class DownloadResponse extends \OCP\AppFramework\Http\Response {

	private $filename;
	private $contentType;

	/**
	 * Creates a response that prompts the user to download the file
	 * @param string $filename the name that the downloaded file should have
	 * @param string $contentType the mimetype that the downloaded file should have
	 * @since 7.0.0
	 */
	public function __construct($filename, $contentType) {
		$this->filename = $filename;
		$this->contentType = $contentType;

		$this->addHeader('Content-Disposition', 'attachment; filename="' . $filename . '"');
		$this->addHeader('Content-Type', $contentType);
	}


}
