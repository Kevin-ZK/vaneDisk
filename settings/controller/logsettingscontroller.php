<?php

namespace OC\Settings\Controller;

use OCP\AppFramework\Controller;
use OCP\AppFramework\Http;
use OCP\AppFramework\Http\JSONResponse;
use OCP\AppFramework\Http\StreamResponse;
use OCP\IL10N;
use OCP\IRequest;
use OCP\IConfig;

/**
 * Class LogSettingsController
 *
 * @package OC\Settings\Controller
 */
class LogSettingsController extends Controller {
	/**
	 * @var \OCP\IConfig
	 */
	private $config;

	/**
	 * @var \OCP\IL10N
	 */
	private $l10n;

	/**
	 * @param string $appName
	 * @param IRequest $request
	 * @param IConfig $config
	 */
	public function __construct($appName,
								IRequest $request,
								IConfig $config,
								IL10N $l10n) {
		parent::__construct($appName, $request);
		$this->config = $config;
		$this->l10n = $l10n;
	}

	/**
	 * set log level for logger
	 *
	 * @param int $level
	 * @return JSONResponse
	 */
	public function setLogLevel($level) {
		if ($level < 0 || $level > 4) {
			return new JSONResponse([
				'message' => (string) $this->l10n->t('log-level out of allowed range'),
			], Http::STATUS_BAD_REQUEST);
		}

		$this->config->setSystemValue('loglevel', $level);
		return new JSONResponse([
			'level' => $level,
		]);
	}

	/**
	 * get log entries from logfile
	 *
	 * @param int $count
	 * @param int $offset
	 * @return JSONResponse
	 */
	public function getEntries($count=50, $offset=0) {
		return new JSONResponse([
			'data' => \OC_Log_Owncloud::getEntries($count, $offset),
			'remain' => count(\OC_Log_Owncloud::getEntries(1, $offset + $count)) !== 0,
		]);
	}

	/**
	 * download logfile
	 *
	 * @NoCSRFRequired
	 *
	 * @return StreamResponse
	 */
	public function download() {
		$resp = new StreamResponse(\OC_Log_Owncloud::getLogFilePath());
		$resp->addHeader('Content-Disposition', 'attachment; filename="owncloud.log"');
		return $resp;
	}
}
