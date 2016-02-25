<?php
namespace OCP;

/**
 * Functions that don't have any specific interface to place
 * @since 6.0.0
 * @deprecated 8.1.0
 */
interface IHelper {
	/**
	 * Gets the content of an URL by using CURL or a fallback if it is not
	 * installed
	 * @param string $url the url that should be fetched
	 * @return string the content of the webpage
	 * @since 6.0.0
	 * @deprecated 8.1.0 Use \OCP\IServerContainer::getHTTPClientService
	 */
	public function getUrlContent($url);
}
