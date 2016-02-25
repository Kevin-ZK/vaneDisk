<?php

namespace OCA\Encryption;


use OCA\Encryption\Hooks\Contracts\IHook;

class HookManager {

	private $hookInstances = [];

	/**
	 * @param array|IHook $instances
	 *        - This accepts either a single instance of IHook or an array of instances of IHook
	 * @return bool
	 */
	public function registerHook($instances) {
		if (is_array($instances)) {
			foreach ($instances as $instance) {
				if (!$instance instanceof IHook) {
					return false;
				}
				$this->hookInstances[] = $instance;
			}

		} elseif ($instances instanceof IHook) {
			$this->hookInstances[] = $instances;
		}
		return true;
	}

	public function fireHooks() {
		foreach ($this->hookInstances as $instance) {
			/**
			 * Fire off the add hooks method of each instance stored in cache
			 *
			 * @var $instance IHook
			 */
			$instance->addHooks();
		}

	}

}
