<?php


namespace OCP\AppFramework\Utility;


/**
 * Needed to mock calls to time()
 * @since 8.0.0
 */
interface ITimeFactory {

	/**
	 * @return int the result of a call to time()
	 * @since 8.0.0
	 */
	public function getTime();

}
