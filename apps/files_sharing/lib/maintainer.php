<?php

namespace OCA\Files\Share;

/**
 * Maintains stuff around the sharing functionality
 *
 * for example: on disable of "allow links" it removes all link shares
 */

class Maintainer {

	/**
	 * Keeps track of the "allow links" config setting
	 * and removes all link shares if the config option is set to "no"
	 *
	 * @param array $params array with app, key, value as named values
	 */
	static public function configChangeHook($params) {
		if($params['app'] === 'core' && $params['key'] === 'shareapi_allow_links' && $params['value'] === 'no') {
			\OCP\Share::removeAllLinkShares();
		}
	}

}
