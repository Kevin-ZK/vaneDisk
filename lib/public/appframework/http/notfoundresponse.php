<?php

namespace OCP\AppFramework\Http;

use OCP\AppFramework\Http;
use OCP\Template;

/**
 * A generic 404 response showing an 404 error page as well to the end-user
 * @since 8.1.0
 */
class NotFoundResponse extends Response {

	/**
	 * @since 8.1.0
	 */
	public function __construct() {
		$this->setStatus(404);
	}

	/**
	 * @return string
	 * @since 8.1.0
	 */
	public function render() {
		$template = new Template('core', '404', 'guest');
		return $template->fetchPage();
	}
}
