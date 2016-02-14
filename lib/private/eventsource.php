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

/**
 * wrapper for server side events (http://en.wikipedia.org/wiki/Server-sent_events)
 * includes a fallback for older browsers and IE
 *
 * use server side events with caution, to many open requests can hang the server
 */
class OC_EventSource implements \OCP\IEventSource {
	/**
	 * @var bool
	 */
	private $fallback;

	/**
	 * @var int
	 */
	private $fallBackId = 0;

	/**
	 * @var bool
	 */
	private $started = false;

	protected function init() {
		if ($this->started) {
			return;
		}
		$this->started = true;

		// prevent php output buffering, caching and nginx buffering
		OC_Util::obEnd();
		header('Cache-Control: no-cache');
		header('X-Accel-Buffering: no');
		$this->fallback = isset($_GET['fallback']) and $_GET['fallback'] == 'true';
		if ($this->fallback) {
			$this->fallBackId = (int)$_GET['fallback_id'];
			header("Content-Type: text/html");
			echo str_repeat('<span></span>' . PHP_EOL, 10); //dummy data to keep IE happy
		} else {
			header("Content-Type: text/event-stream");
		}
		if (!OC_Util::isCallRegistered()) {
			$this->send('error', 'Possible CSRF attack. Connection will be closed.');
			$this->close();
			exit();
		}
		flush();
	}

	/**
	 * send a message to the client
	 *
	 * @param string $type
	 * @param mixed $data
	 *
	 * @throws \BadMethodCallException
	 * if only one parameter is given, a typeless message will be send with that parameter as data
	 */
	public function send($type, $data = null) {
		if ($data and !preg_match('/^[A-Za-z0-9_]+$/', $type)) {
			throw new BadMethodCallException('Type needs to be alphanumeric ('. $type .')');
		}
		$this->init();
		if (is_null($data)) {
			$data = $type;
			$type = null;
		}
		if ($this->fallback) {
			$response = '<script type="text/javascript">window.parent.OC.EventSource.fallBackCallBack('
				. $this->fallBackId . ',"' . $type . '",' . OCP\JSON::encode($data) . ')</script>' . PHP_EOL;
			echo $response;
		} else {
			if ($type) {
				echo 'event: ' . $type . PHP_EOL;
			}
			echo 'data: ' . OCP\JSON::encode($data) . PHP_EOL;
		}
		echo PHP_EOL;
		flush();
	}

	/**
	 * close the connection of the event source
	 */
	public function close() {
		$this->send('__internal__', 'close'); //server side closing can be an issue, let the client do it
	}
}
