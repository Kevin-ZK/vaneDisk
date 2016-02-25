<?php

namespace OC;

use \OCP\ILogger;
use OCP\Security\StringUtils;

/**
 * logging utilities
 *
 * This is a stand in, this should be replaced by a Psr\Log\LoggerInterface
 * compatible logger. See https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-3-logger-interface.md
 * for the full interface specification.
 *
 * MonoLog is an example implementing this interface.
 */

class Log implements ILogger {

	/** @var string */
	private $logger;
	/** @var SystemConfig */
	private $config;

	/** @var boolean|null cache the result of the log condition check for the request */
	private $logConditionSatisfied = null;

	/**
	 * @param string $logger The logger that should be used
	 * @param SystemConfig $config the system config object
	 */
	public function __construct($logger=null, SystemConfig $config=null) {
		// FIXME: Add this for backwards compatibility, should be fixed at some point probably
		if($config === null) {
			$config = \OC::$server->getSystemConfig();
		}

		$this->config = $config;

		// FIXME: Add this for backwards compatibility, should be fixed at some point probably
		if($logger === null) {
			$this->logger = 'OC_Log_'.ucfirst($this->config->getValue('log_type', 'owncloud'));
			call_user_func(array($this->logger, 'init'));
		} else {
			$this->logger = $logger;
		}

	}


	/**
	 * System is unusable.
	 *
	 * @param string $message
	 * @param array $context
	 */
	public function emergency($message, array $context = array()) {
		$this->log(\OC_Log::FATAL, $message, $context);
	}

	/**
	 * Action must be taken immediately.
	 *
	 * Example: Entire website down, database unavailable, etc. This should
	 * trigger the SMS alerts and wake you up.
	 *
	 * @param string $message
	 * @param array $context
	 */
	public function alert($message, array $context = array()) {
		$this->log(\OC_Log::ERROR, $message, $context);
	}

	/**
	 * Critical conditions.
	 *
	 * Example: Application component unavailable, unexpected exception.
	 *
	 * @param string $message
	 * @param array $context
	 */
	public function critical($message, array $context = array()) {
		$this->log(\OC_Log::ERROR, $message, $context);
	}

	/**
	 * Runtime errors that do not require immediate action but should typically
	 * be logged and monitored.
	 *
	 * @param string $message
	 * @param array $context
	 */
	public function error($message, array $context = array()) {
		$this->log(\OC_Log::ERROR, $message, $context);
	}

	/**
	 * Exceptional occurrences that are not errors.
	 *
	 * Example: Use of deprecated APIs, poor use of an API, undesirable things
	 * that are not necessarily wrong.
	 *
	 * @param string $message
	 * @param array $context
	 */
	public function warning($message, array $context = array()) {
		$this->log(\OC_Log::WARN, $message, $context);
	}

	/**
	 * Normal but significant events.
	 *
	 * @param string $message
	 * @param array $context
	 */
	public function notice($message, array $context = array()) {
		$this->log(\OC_Log::INFO, $message, $context);
	}

	/**
	 * Interesting events.
	 *
	 * Example: User logs in, SQL logs.
	 *
	 * @param string $message
	 * @param array $context
	 */
	public function info($message, array $context = array()) {
		$this->log(\OC_Log::INFO, $message, $context);
	}

	/**
	 * Detailed debug information.
	 *
	 * @param string $message
	 * @param array $context
	 */
	public function debug($message, array $context = array()) {
		$this->log(\OC_Log::DEBUG, $message, $context);
	}


	/**
	 * Logs with an arbitrary level.
	 *
	 * @param mixed $level
	 * @param string $message
	 * @param array $context
	 */
	public function log($level, $message, array $context = array()) {
		$minLevel = min($this->config->getValue('loglevel', \OC_Log::WARN), \OC_Log::ERROR);
		$logCondition = $this->config->getValue('log.condition', []);

		if (isset($context['app'])) {
			$app = $context['app'];

			/**
			 * check log condition based on the context of each log message
			 * once this is met -> change the required log level to debug
			 */
			if(!empty($logCondition)
				&& isset($logCondition['apps'])
				&& in_array($app, $logCondition['apps'], true)) {
				$minLevel = \OC_Log::DEBUG;
			}

		} else {
			$app = 'no app in context';
		}
		// interpolate $message as defined in PSR-3
		$replace = array();
		foreach ($context as $key => $val) {
			$replace['{' . $key . '}'] = $val;
		}

		// interpolate replacement values into the message and return
		$message = strtr($message, $replace);

		/**
		 * check for a special log condition - this enables an increased log on
		 * a per request/user base
		 */
		if($this->logConditionSatisfied === null) {
			// default to false to just process this once per request
			$this->logConditionSatisfied = false;
			if(!empty($logCondition)) {

				// check for secret token in the request
				if(isset($logCondition['shared_secret'])) {
					$request = \OC::$server->getRequest();

					// if token is found in the request change set the log condition to satisfied
					if($request && StringUtils::equals($request->getParam('log_secret'), $logCondition['shared_secret'])) {
						$this->logConditionSatisfied = true;
					}
				}

				// check for user
				if(isset($logCondition['users'])) {
					$user = \OC::$server->getUserSession()->getUser();

					// if the user matches set the log condition to satisfied
					if($user !== null && in_array($user->getUID(), $logCondition['users'], true)) {
						$this->logConditionSatisfied = true;
					}
				}
			}
		}

		// if log condition is satisfied change the required log level to DEBUG
		if($this->logConditionSatisfied) {
			$minLevel = \OC_Log::DEBUG;
		}

		if ($level >= $minLevel) {
			$logger = $this->logger;
			call_user_func(array($logger, 'write'), $app, $message, $level);
		}
	}
}
