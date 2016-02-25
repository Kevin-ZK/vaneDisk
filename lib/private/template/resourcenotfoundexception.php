<?php

namespace OC\Template;

class ResourceNotFoundException extends \LogicException {
	protected $resource;
	protected $webPath;

	/**
	 * @param string $resource
	 * @param string $webPath
	 */
	public function __construct($resource, $webPath) {
		parent::__construct('Resource not found');
		$this->resource = $resource;
		$this->webPath = $webPath;
	}

	/**
	 * @return string
	 */
	public function getResourcePath() {
		return $this->webPath . '/' . $this->resource;
	}
}
