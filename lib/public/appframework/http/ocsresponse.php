<?php

namespace OCP\AppFramework\Http;

use OCP\AppFramework\Http;

use OC_OCS;

/**
 * A renderer for OCS responses
 * @since 8.1.0
 */
class OCSResponse extends Response {

	private $data;
	private $format;
	private $statuscode;
	private $message;
	private $tag;
	private $tagattribute;
	private $dimension;
	private $itemscount;
	private $itemsperpage;

	/**
	 * generates the xml or json response for the API call from an multidimenional data array.
	 * @param string $format
	 * @param string $status
	 * @param string $statuscode
	 * @param string $message
	 * @param array $data
	 * @param string $tag
	 * @param string $tagattribute
	 * @param int $dimension
	 * @param int|string $itemscount
	 * @param int|string $itemsperpage
	 * @since 8.1.0
	 */
	public function __construct($format, $status, $statuscode, $message,
								$data=[], $tag='', $tagattribute='',
								$dimension=-1, $itemscount='',
								$itemsperpage='') {
		$this->format = $format;
		$this->setStatus($status);
		$this->statuscode = $statuscode;
		$this->message = $message;
		$this->data = $data;
		$this->tag = $tag;
		$this->tagattribute = $tagattribute;
		$this->dimension = $dimension;
		$this->itemscount = $itemscount;
		$this->itemsperpage = $itemsperpage;

		// set the correct header based on the format parameter
		if ($format === 'json') {
			$this->addHeader(
				'Content-Type', 'application/json; charset=utf-8'
			);
		} else {
			$this->addHeader(
				'Content-Type', 'application/xml; charset=utf-8'
			);
		}
	}

	/**
	 * @return string
	 * @since 8.1.0
	 */
	public function render() {
		return OC_OCS::generateXml(
			$this->format, $this->getStatus(), $this->statuscode, $this->message,
			$this->data, $this->tag, $this->tagattribute, $this->dimension,
			$this->itemscount, $this->itemsperpage
		);
	}


}
