<?php

namespace OC;

/**
 * Class AppHelper
 * @deprecated 8.1.0
 */
class AppHelper implements \OCP\IHelper {
	/**
	 * Gets the content of an URL by using CURL or a fallback if it is not
	 * installed
	 * @param string $url the url that should be fetched
	 * @return string the content of the webpage
	 * @deprecated 8.1.0 Use \OCP\IServerContainer::getHTTPClientService
	 */
	public function getUrlContent($url) {
		return \OC_Util::getUrlContent($url);
	}
}
