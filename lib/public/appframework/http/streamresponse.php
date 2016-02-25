<?php

namespace OCP\AppFramework\Http;

use OCP\AppFramework\Http;

/**
 * Class StreamResponse
 *
 * @package OCP\AppFramework\Http
 * @since 8.1.0
 */
class StreamResponse extends Response implements ICallbackResponse {
	/** @var string */
	private $filePath;

	/**
	 * @param string $filePath the path to the file which should be streamed
	 * @since 8.1.0
	 */
	public function __construct ($filePath) {
		$this->filePath = $filePath;
	}


	/**
	 * Streams the file using readfile
	 *
	 * @param IOutput $output a small wrapper that handles output
	 * @since 8.1.0
	 */
	public function callback (IOutput $output) {
		// handle caching
		if ($output->getHttpResponseCode() !== Http::STATUS_NOT_MODIFIED) {
			if (!file_exists($this->filePath)) {
				$output->setHttpResponseCode(Http::STATUS_NOT_FOUND);
			} elseif ($output->setReadfile($this->filePath) === false) {
				$output->setHttpResponseCode(Http::STATUS_BAD_REQUEST);
			}
		}
	}

}
