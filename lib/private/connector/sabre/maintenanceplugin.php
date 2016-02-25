<?php

namespace OC\Connector\Sabre;

use OCP\IConfig;
use Sabre\DAV\Exception\ServiceUnavailable;
use Sabre\DAV\ServerPlugin;

class MaintenancePlugin extends ServerPlugin {

	/** @var IConfig */
	private $config;

	/**
	 * Reference to main server object
	 *
	 * @var Server
	 */
	private $server;

	/**
	 * @param IConfig $config
	 */
	public function __construct(IConfig $config = null) {
		$this->config = $config;
		if (is_null($config)) {
			$this->config = \OC::$server->getConfig();
		}
	}


	/**
	 * This initializes the plugin.
	 *
	 * This function is called by \Sabre\DAV\Server, after
	 * addPlugin is called.
	 *
	 * This method should set up the required event subscriptions.
	 *
	 * @param \Sabre\DAV\Server $server
	 * @return void
	 */
	public function initialize(\Sabre\DAV\Server $server) {
		$this->server = $server;
		$this->server->on('beforeMethod', array($this, 'checkMaintenanceMode'), 10);
	}

	/**
	 * This method is called before any HTTP method and returns http status code 503
	 * in case the system is in maintenance mode.
	 *
	 * @throws ServiceUnavailable
	 * @return bool
	 */
	public function checkMaintenanceMode() {
		if ($this->config->getSystemValue('singleuser', false)) {
			throw new ServiceUnavailable('System in single user mode.');
		}
		if ($this->config->getSystemValue('maintenance', false)) {
			throw new ServiceUnavailable('System in maintenance mode.');
		}
		if (\OC::checkUpgrade(false)) {
			throw new ServiceUnavailable('Upgrade needed');
		}

		return true;
	}
}
