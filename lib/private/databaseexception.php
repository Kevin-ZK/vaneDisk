<?php

namespace OC;

class DatabaseException extends \Exception {
	private $query;

	//FIXME getQuery seems to be unused, maybe use parent constructor with $message, $code and $previous
	public function __construct($message, $query = null){
		parent::__construct($message);
		$this->query = $query;
	}

	public function getQuery() {
		return $this->query;
	}
}
