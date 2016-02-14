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
$dir = '/';

if (isset($_GET['dir'])) {
	$dir = (string)$_GET['dir'];
}

OCP\JSON::checkLoggedIn();
\OC::$server->getSession()->close();

// send back json
try {
	OCP\JSON::success(array('data' => \OCA\Files\Helper::buildFileStorageStatistics($dir)));
} catch (\OCP\Files\NotFoundException $e) {
	OCP\JSON::error(['data' => ['message' => 'Folder not found']]);
}
