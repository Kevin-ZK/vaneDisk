<?php

namespace OCA\user_ldap\lib;

class WizardResult {
	protected $changes = array();
	protected $options = array();
	protected $markedChange = false;

	/**
	 * @param string $key
	 * @param mixed $value
	 */
	public function addChange($key, $value) {
		$this->changes[$key] = $value;
	}

	/**
	 *
	 */
	public function markChange() {
		$this->markedChange = true;
	}

	/**
	 * @param string $key
	 * @param array|string $values
	 */
	public function addOptions($key, $values) {
		if(!is_array($values)) {
			$values = array($values);
		}
		$this->options[$key] = $values;
	}

	/**
	 * @return bool
	 */
	public function hasChanges() {
		return (count($this->changes) > 0 || $this->markedChange);
	}

	/**
	 * @return array
	 */
	public function getResultArray() {
		$result = array();
		$result['changes'] = $this->changes;
		if(count($this->options) > 0) {
			$result['options'] = $this->options;
		}
		return $result;
	}
}
