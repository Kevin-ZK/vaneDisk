<?php
namespace OCP\AppFramework\Http;

/**
 * Class DataDownloadResponse
 *
 * @package OCP\AppFramework\Http
 * @since 8.0.0
 */
class DataDownloadResponse extends DownloadResponse {
	/**
	 * @var string
	 */
	private $data;

	/**
	 * Creates a response that prompts the user to download the text
	 * @param string $data text to be downloaded
	 * @param string $filename the name that the downloaded file should have
	 * @param string $contentType the mimetype that the downloaded file should have
	 * @since 8.0.0
	 */
	public function __construct($data, $filename, $contentType) {
		$this->data = $data;
		parent::__construct($filename, $contentType);
	}

	/**
	 * @param string $data
	 * @since 8.0.0
	 */
	public function setData($data) {
		$this->data = $data;
	}

	/**
	 * @return string
	 * @since 8.0.0
	 */
	public function render() {
		return $this->data;
	}
}
