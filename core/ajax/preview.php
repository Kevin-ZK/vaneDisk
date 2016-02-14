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
\OC_Util::checkLoggedIn();
\OC::$server->getSession()->close();

$file = array_key_exists('file', $_GET) ? (string)$_GET['file'] : '';
$maxX = array_key_exists('x', $_GET) ? (int)$_GET['x'] : '36';
$maxY = array_key_exists('y', $_GET) ? (int)$_GET['y'] : '36';
$scalingUp = array_key_exists('scalingup', $_GET) ? (bool)$_GET['scalingup'] : true;
$keepAspect = array_key_exists('a', $_GET) ? true : false;
$always = array_key_exists('forceIcon', $_GET) ? (bool)$_GET['forceIcon'] : true;

if ($file === '') {
	//400 Bad Request
	\OC_Response::setStatus(400);
	\OC_Log::write('core-preview', 'No file parameter was passed', \OC_Log::DEBUG);
	exit;
}

if ($maxX === 0 || $maxY === 0) {
	//400 Bad Request
	\OC_Response::setStatus(400);
	\OC_Log::write('core-preview', 'x and/or y set to 0', \OC_Log::DEBUG);
	exit;
}

$info = \OC\Files\Filesystem::getFileInfo($file);

if (!$info instanceof OCP\Files\FileInfo || !$always && !\OC::$server->getPreviewManager()->isAvailable($info)) {
	\OC_Response::setStatus(404);
} else {
	$preview = new \OC\Preview(\OC_User::getUser(), 'files');
	$preview->setFile($file);
	$preview->setMaxX($maxX);
	$preview->setMaxY($maxY);
	$preview->setScalingUp($scalingUp);
	$preview->setKeepAspect($keepAspect);
	$preview->showPreview();
}
