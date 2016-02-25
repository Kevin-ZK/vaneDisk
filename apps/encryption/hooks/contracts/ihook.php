<?php

namespace OCA\Encryption\Hooks\Contracts;


interface IHook {
	/**
	 * Connects Hooks
	 *
	 * @return null
	 */
	public function addHooks();
}
