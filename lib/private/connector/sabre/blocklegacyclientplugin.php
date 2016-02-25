<?php

namespace OC\Connector\Sabre;

use OCP\IConfig;
use Sabre\HTTP\RequestInterface;
use Sabre\DAV\ServerPlugin;
use Sabre\DAV\Exception;

/**
 * Class BlockLegacyClientPlugin is used to detect old legacy sync clients and
 * returns a 403 status to those clients
 *
 * @package OC\Connector\Sabre
 */
class BlockLegacyClientPlugin extends ServerPlugin {
	/** @var \Sabre\DAV\Server */
	protected $server;
	/** @var IConfig */
	protected $config;

	/**
	 * @param IConfig $config
	 */
	public function __construct(IConfig $config) {
		$this->config = $config;
	}

	/**
	 * @param \Sabre\DAV\Server $server
	 * @return void
	 */
	public function initialize(\Sabre\DAV\Server $server) {
		$this->server = $server;
		$this->server->on('beforeMethod', [$this, 'beforeHandler'], 200);
	}

	/**
	 * Detects all unsupported clients and throws a \Sabre\DAV\Exception\Forbidden
	 * exception which will result in a 403 to them.
	 * @param RequestInterface $request
	 * @throws \Sabre\DAV\Exception\Forbidden If the client version is not supported
	 */
	public function beforeHandler(RequestInterface $request) {
		$userAgent = $request->getHeader('User-Agent');
		if($userAgent === null) {
			return;
		}

		$minimumSupportedDesktopVersion = $this->config->getSystemValue('minimum.supported.desktop.version', '1.7.0');

		// Match on the mirall version which is in scheme "Mozilla/5.0 (%1) mirall/%2" or
		// "mirall/%1" for older releases
		preg_match("/(?:mirall\\/)([\d.]+)/i", $userAgent, $versionMatches);
		if(isset($versionMatches[1]) &&
			version_compare($versionMatches[1], $minimumSupportedDesktopVersion) === -1) {
			throw new \Sabre\DAV\Exception\Forbidden('Unsupported client version.');
		}
	}
}
