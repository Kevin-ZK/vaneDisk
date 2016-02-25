<?php

OCP\JSON::checkAppEnabled('files_versions');
OCP\JSON::checkLoggedIn();

$file = $_GET['file'];
$revision=(int)$_GET['revision'];

list($uid, $filename) = OCA\Files_Versions\Storage::getUidAndFilename($file);

$versionName = '/'.$uid.'/files_versions/'.$filename.'.v'.$revision;

$view = new OC\Files\View('/');

$ftype = \OC_Helper::getSecureMimeType($view->getMimeType('/'.$uid.'/files/'.$filename));

header('Content-Type:'.$ftype);
OCP\Response::setContentDispositionHeader(basename($filename), 'attachment');
OCP\Response::disableCaching();
OCP\Response::setContentLengthHeader($view->filesize($versionName));

OC_Util::obEnd();

$view->readfile($versionName);
