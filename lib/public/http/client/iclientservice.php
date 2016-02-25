<?php

namespace OCP\Http\Client;

/**
 * Interface IClientService
 *
 * @package OCP\Http
 * @since 8.1.0
 */
interface IClientService {
	/**
	 * @return IClient
	 * @since 8.1.0
	 */
	public function newClient();
}
