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

// Check if we are a user
OCP\User::checkLoggedIn();

$filename = $_GET["file"];

if(!\OC\Files\Filesystem::file_exists($filename)) {
	header("HTTP/1.0 404 Not Found");
	$tmpl = new OCP\Template( '', '404', 'guest' );
	$tmpl->assign('file', $filename);
	$tmpl->printPage();
	exit;
}

$ftype=\OC_Helper::getSecureMimeType(\OC\Files\Filesystem::getMimeType( $filename ));

header('Content-Type:'.$ftype);
OCP\Response::setContentDispositionHeader(basename($filename), 'attachment');
OCP\Response::disableCaching();
OCP\Response::setContentLengthHeader(\OC\Files\Filesystem::filesize($filename));

OC_Util::obEnd();
\OC\Files\Filesystem::readfile( $filename );
