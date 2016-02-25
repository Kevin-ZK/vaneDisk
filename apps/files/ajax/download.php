<?php

// Check if we are a user
OCP\User::checkLoggedIn();
\OC::$server->getSession()->close();

$files = isset($_GET['files']) ? (string)$_GET['files'] : '';
$dir = isset($_GET['dir']) ? (string)$_GET['dir'] : '';

$files_list = json_decode($files);
// in case we get only a single file
if (!is_array($files_list)) {
	$files_list = array($files);
}

OC_Files::get($dir, $files_list, $_SERVER['REQUEST_METHOD'] == 'HEAD');
