<?php

namespace OCP\AppFramework\Http;


/**
 * Interface ICallbackResponse
 *
 * @package OCP\AppFramework\Http
 * @since 8.1.0
 */
interface ICallbackResponse {

	/**
	 * Outputs the content that should be printed
	 *
	 * @param IOutput $output a small wrapper that handles output
	 * @since 8.1.0
	 */
	function callback(IOutput $output);

}
