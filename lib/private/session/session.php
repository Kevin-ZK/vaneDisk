<?php

namespace OC\Session;

use OCP\ISession;

abstract class Session implements \ArrayAccess, ISession {

	/**
	 * @var bool
	 */
	protected $sessionClosed = false;

	/**
	 * $name serves as a namespace for the session keys
	 *
	 * @param string $name
	 */
	abstract public function __construct($name);

	/**
	 * @param mixed $offset
	 * @return bool
	 */
	public function offsetExists($offset) {
		return $this->exists($offset);
	}

	/**
	 * @param mixed $offset
	 * @return mixed
	 */
	public function offsetGet($offset) {
		return $this->get($offset);
	}

	/**
	 * @param mixed $offset
	 * @param mixed $value
	 */
	public function offsetSet($offset, $value) {
		$this->set($offset, $value);
	}

	/**
	 * @param mixed $offset
	 */
	public function offsetUnset($offset) {
		$this->remove($offset);
	}

	/**
	 * Close the session and release the lock
	 */
	public function close() {
		$this->sessionClosed = true;
	}
}
