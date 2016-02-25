<?php

namespace OCP\AppFramework\Http;

use OCP\AppFramework\Http;

/**
 * A generic DataResponse class that is used to return generic data responses
 * for responders to transform
 * @since 8.0.0
 */
class DataResponse extends Response {

	/**
	 * response data
	 * @var array|object
	 */
	protected $data;


	/**
	 * @param array|object $data the object or array that should be transformed
	 * @param int $statusCode the Http status code, defaults to 200
	 * @param array $headers additional key value based headers
	 * @since 8.0.0
	 */
	public function __construct($data=array(), $statusCode=Http::STATUS_OK,
	                            array $headers=array()) {
		$this->data = $data;
		$this->setStatus($statusCode);
		$this->setHeaders(array_merge($this->getHeaders(), $headers));
	}


	/**
	 * Sets values in the data json array
	 * @param array|object $data an array or object which will be transformed
	 * @return DataResponse Reference to this object
	 * @since 8.0.0
	 */
	public function setData($data){
		$this->data = $data;

		return $this;
	}


	/**
	 * Used to get the set parameters
	 * @return array the data
	 * @since 8.0.0
	 */
	public function getData(){
		return $this->data;
	}


}
