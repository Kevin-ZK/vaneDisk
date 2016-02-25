<?php


namespace OC\AppFramework\Utility;

use OCP\AppFramework\Utility\ITimeFactory;


/**
 * Needed to mock calls to time()
 */
class TimeFactory implements ITimeFactory {


	/**
	 * @return int the result of a call to time()
	 */
	public function getTime() {
		return time();
	}


}
