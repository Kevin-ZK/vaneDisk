<?php

namespace OCP\Lock;

/**
 * Class LockedException
 *
 * @package OCP\Lock
 * @since 8.1.0
 */
class LockedException extends \Exception {

	/**
	 * Locked path
	 *
	 * @var string
	 */
	private $path;

	/**
	 * LockedException constructor.
	 *
	 * @param string $path locked path
	 * @param \Exception $previous previous exception for cascading
	 *
	 * @since 8.1.0
	 */
	public function __construct($path, \Exception $previous = null) {
		parent::__construct('"' . $path . '" is locked', 0, $previous);
		$this->path = $path;
	}

	/**
	 * @return string
	 * @since 8.1.0
	 */
	public function getPath() {
		return $this->path;
	}
}
