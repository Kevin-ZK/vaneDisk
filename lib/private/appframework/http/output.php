<?php
/**
 *
 * @copyright Copyright (c) 2015, ownCloud, Inc.
 * @license AGPL-3.0
 *
 * This code is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License, version 3,
 * as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License, version 3,
 * along with this program.  If not, see <http://www.gnu.org/licenses/>
 *
 */

namespace OC\AppFramework\Http;

use OCP\AppFramework\Http\IOutput;

/**
 * Very thin wrapper class to make output testable
 */
class Output implements IOutput {

	/**
	 * @param string $out
	 */
	public function setOutput($out) {
		print($out);
	}

	/**
	 * @param string $path
	 *
	 * @return bool false if an error occured
	 */
	public function setReadfile($path) {
		return @readfile($path);
	}

	/**
	 * @param string $header
	 */
	public function setHeader($header) {
		header($header);
	}

	/**
	 * @param int $code sets the http status code
	 */
	public function setHttpResponseCode($code) {
		http_response_code($code);
	}

	/**
	 * @return int returns the current http response code
	 */
	public function getHttpResponseCode() {
		return http_response_code();
	}

	/**
	 * @param string $name
	 * @param string $value
	 * @param int $expire
	 * @param string $path
	 * @param string $domain
	 * @param bool $secure
	 * @param bool $httponly
	 */
	public function setCookie($name, $value, $expire, $path, $domain, $secure, $httponly) {
		setcookie($name, $value, $expire, $path, $domain, $secure, $httponly);
	}

}
