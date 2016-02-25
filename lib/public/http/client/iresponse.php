<?php

namespace OCP\Http\Client;

/**
 * Interface IResponse
 *
 * @package OCP\Http
 * @since 8.1.0
 */
interface IResponse {
	/**
	 * @return string
	 * @since 8.1.0
	 */
	public function getBody();

	/**
	 * @return int
	 * @since 8.1.0
	 */
	public function getStatusCode();

	/**
	 * @param $key
	 * @return string
	 * @since 8.1.0
	 */
	public function getHeader($key);

	/**
	 * @return array
	 * @since 8.1.0
	 */
	public function getHeaders();
}
