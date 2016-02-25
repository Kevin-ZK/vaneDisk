<?php

namespace OC\Connector\Sabre\Exception;

use Sabre\DAV\Exception;

class InvalidPath extends Exception {

	const NS_OWNCLOUD = 'http://owncloud.org/ns';

	/**
	 * @var bool
	 */
	private $retry;

	/**
	 * @param string $message
	 * @param bool $retry
	 */
	public function __construct($message, $retry = false) {
		parent::__construct($message);
		$this->retry = $retry;
	}

	/**
	 * Returns the HTTP status code for this exception
	 *
	 * @return int
	 */
	public function getHTTPCode() {

		return 400;

	}

	/**
	 * This method allows the exception to include additional information
	 * into the WebDAV error response
	 *
	 * @param \Sabre\DAV\Server $server
	 * @param \DOMElement $errorNode
	 * @return void
	 */
	public function serialize(\Sabre\DAV\Server $server,\DOMElement $errorNode) {

		// set ownCloud namespace
		$errorNode->setAttribute('xmlns:o', self::NS_OWNCLOUD);

		// adding the retry node
		$error = $errorNode->ownerDocument->createElementNS('o:','o:retry', var_export($this->retry, true));
		$errorNode->appendChild($error);

		// adding the message node
		$error = $errorNode->ownerDocument->createElementNS('o:','o:reason', $this->getMessage());
		$errorNode->appendChild($error);
	}

}
