<?php

namespace OC\L10N;

/**
 * TODO: Description
 */
class Factory {
	/**
	 * cached instances
	 */
	protected $instances = array();

	/**
	 * get an L10N instance
	 *
	 * @param string $app
	 * @param string|null $lang
	 * @return \OC_L10N
	 */
	public function get($app, $lang = null) {
		if (!is_null($lang)) {
			return new \OC_L10N($app, $lang);
		} else if (!isset($this->instances[$app])) {
			$this->instances[$app] = new \OC_L10N($app);
		}
		return $this->instances[$app];
	}

}
