<?php

namespace OCA\TemplateEditor\Http;

use OCP\AppFramework\Http\Response;

/**
 * Prompts the user to download the a file
 */
class MailTemplateResponse extends Response {

	private $filename;
	private $contentType;

	/**
	 * Creates a response that prompts the user to download the file
	 * @param string $filename the name that the downloaded file should have
	 * @param string $contentType the mime type that the downloaded file should have
	 */
	public function __construct($filename, $contentType = 'text/php') {
		$this->filename = $filename;
		$this->contentType = $contentType;

		$this->addHeader('Content-Disposition', 'attachment; filename="' . $filename . '"');
		$this->addHeader('Content-Type', $contentType);
	}

	/**
	 * Returns the raw template content
	 * @return string the file
	 */
	public function render(){
		return file_get_contents($this->filename);
	}

}
