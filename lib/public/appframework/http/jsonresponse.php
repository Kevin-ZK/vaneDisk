<?php

namespace OCP\AppFramework\Http;

use OCP\AppFramework\Http;

/**
 * A renderer for JSON calls
 * @since 6.0.0
 */
class JSONResponse extends Response {

	/**
	 * response data
	 * @var array|object
	 */
	protected $data;


	/**
	 * constructor of JSONResponse
	 * @param array|object $data the object or array that should be transformed
	 * @param int $statusCode the Http status code, defaults to 200
	 * @since 6.0.0
	 */
	public function __construct($data=array(), $statusCode=Http::STATUS_OK) {
		$this->data = $data;
		$this->setStatus($statusCode);
		$this->addHeader('Content-Type', 'application/json; charset=utf-8');
	}


	/**
	 * Returns the rendered json
	 * @return string the rendered json
	 * @since 6.0.0
	 */
	public function render(){
		return json_encode($this->data);
	}

	/**
	 * Sets values in the data json array
	 * @param array|object $data an array or object which will be transformed
	 *                             to JSON
	 * @return JSONResponse Reference to this object
	 * @since 6.0.0 - return value was added in 7.0.0
	 */
	public function setData($data){
		$this->data = $data;

		return $this;
	}


	/**
	 * Used to get the set parameters
	 * @return array the data
	 * @since 6.0.0
	 */
	public function getData(){
		return $this->data;
	}

}
