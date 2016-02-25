<?php

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
