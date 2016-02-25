<?php

OCP\JSON::checkAppEnabled('files_sharing');

if(!isset($_GET['t'])){
	\OC_Response::setStatus(\OC_Response::STATUS_BAD_REQUEST);
	\OC_Log::write('core-preview', 'No token parameter was passed', \OC_Log::DEBUG);
	exit;
}

$token = $_GET['t'];

$password = null;
if (isset($_POST['password'])) {
	$password = $_POST['password'];
}

$relativePath = null;
if (isset($_GET['dir'])) {
	$relativePath = $_GET['dir'];
}

$sortAttribute = isset( $_GET['sort'] ) ? $_GET['sort'] : 'name';
$sortDirection = isset( $_GET['sortdirection'] ) ? ($_GET['sortdirection'] === 'desc') : false;

$data = \OCA\Files_Sharing\Helper::setupFromToken($token, $relativePath, $password);

$linkItem = $data['linkItem'];
// Load the files
$dir = $data['realPath'];

$dir = \OC\Files\Filesystem::normalizePath($dir);
if (!\OC\Files\Filesystem::is_dir($dir . '/')) {
	\OC_Response::setStatus(\OC_Response::STATUS_NOT_FOUND);
	\OCP\JSON::error(array('success' => false));
	exit();
}

$data = array();

// make filelist
$files = \OCA\Files\Helper::getFiles($dir, $sortAttribute, $sortDirection);

$formattedFiles = array();
foreach ($files as $file) {
	$entry = \OCA\Files\Helper::formatFileInfo($file);
	// for now
	unset($entry['directory']);
	// do not disclose share owner
	unset($entry['shareOwner']);
	$entry['permissions'] = \OCP\Constants::PERMISSION_READ;
	$formattedFiles[] = $entry;
}

$data['directory'] = $relativePath;
$data['files'] = $formattedFiles;
$data['dirToken'] = $linkItem['token'];

$permissions = $linkItem['permissions'];

// if globally disabled
if (\OC::$server->getAppConfig()->getValue('core', 'shareapi_allow_public_upload', 'yes') === 'no') {
	// only allow reading
	$permissions = \OCP\Constants::PERMISSION_READ;
}

$data['permissions'] = $permissions;

OCP\JSON::success(array('data' => $data));
