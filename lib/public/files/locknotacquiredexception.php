<?php
namespace OCP\Files;

/**
 * Exception for a file that is locked
 * @since 7.0.0
 */
class LockNotAcquiredException extends \Exception {
	/** @var string $path The path that could not be locked */
	public $path;

	/** @var integer $lockType The type of the lock that was attempted */
	public $lockType;

	/**
	 * @since 7.0.0
	 */
	public function __construct($path, $lockType, $code = 0, \Exception $previous = null) {
		$message = \OC::$server->getL10N('core')->t('Could not obtain lock type %d on "%s".', array($lockType, $path));
		parent::__construct($message, $code, $previous);
	}

	/**
	 * custom string representation of object
	 *
	 * @return string
	 * @since 7.0.0
	 */
	public function __toString() {
		return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
	}
}
