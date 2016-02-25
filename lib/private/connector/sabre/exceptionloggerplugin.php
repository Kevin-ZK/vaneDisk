<?php

namespace OC\Connector\Sabre;

use OCP\ILogger;
use Sabre\DAV\Exception;
use Sabre\HTTP\Response;

class ExceptionLoggerPlugin extends \Sabre\DAV\ServerPlugin {
	private $nonFatalExceptions = array(
		'Sabre\DAV\Exception\NotAuthenticated' => true,
		// the sync client uses this to find out whether files exist,
		// so it is not always an error, log it as debug
		'Sabre\DAV\Exception\NotFound' => true,
		// this one mostly happens when the same file is uploaded at
		// exactly the same time from two clients, only one client
		// wins, the second one gets "Precondition failed"
		'Sabre\DAV\Exception\PreconditionFailed' => true,
		// forbidden can be expected when trying to upload to
		// read-only folders for example
		'Sabre\DAV\Exception\Forbidden' => true,
	);

	/** @var string */
	private $appName;

	/** @var ILogger */
	private $logger;

	/**
	 * @param string $loggerAppName app name to use when logging
	 * @param ILogger $logger
	 */
	public function __construct($loggerAppName, $logger) {
		$this->appName = $loggerAppName;
		$this->logger = $logger;
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

		$server->on('exception', array($this, 'logException'), 10);
	}

	/**
	 * Log exception
	 *
	 */
	public function logException(\Exception $ex) {
		$exceptionClass = get_class($ex);
		$level = \OCP\Util::FATAL;
		if (isset($this->nonFatalExceptions[$exceptionClass])) {
			$level = \OCP\Util::DEBUG;
		}

		$message = $ex->getMessage();
		if ($ex instanceof Exception) {
			if (empty($message)) {
				$response = new Response($ex->getHTTPCode());
				$message = $response->getStatusText();
			}
			$message = "HTTP/1.1 {$ex->getHTTPCode()} $message";
		}

		$exception = [
			'Message' => $message,
			'Code' => $ex->getCode(),
			'Trace' => $ex->getTraceAsString(),
			'File' => $ex->getFile(),
			'Line' => $ex->getLine(),
		];
		$this->logger->log($level, 'Exception: ' . json_encode($exception), ['app' => $this->appName]);
	}
}
