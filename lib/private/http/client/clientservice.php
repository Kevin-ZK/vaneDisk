<?php

namespace OC\Http\Client;

use GuzzleHttp\Client as GuzzleClient;
use OCP\Http\Client\IClientService;
use OCP\ICertificateManager;
use OCP\IConfig;

/**
 * Class ClientService
 *
 * @package OC\Http
 */
class ClientService implements IClientService {
	/** @var IConfig */
	private $config;
	/** @var ICertificateManager */
	private $certificateManager;

	/**
	 * @param IConfig $config
	 * @param ICertificateManager $certificateManager
	 */
	public function __construct(IConfig $config,
								ICertificateManager $certificateManager) {
		$this->config = $config;
		$this->certificateManager = $certificateManager;
	}

	/**
	 * @return Client
	 */
	public function newClient() {
		return new Client($this->config, $this->certificateManager, new GuzzleClient());
	}
}
