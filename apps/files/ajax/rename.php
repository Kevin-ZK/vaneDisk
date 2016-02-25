<?php

OCP\JSON::checkLoggedIn();
OCP\JSON::callCheck();
\OC::$server->getSession()->close();

$l10n = \OC::$server->getL10N('files');

$files = new \OCA\Files\App(
	\OC\Files\Filesystem::getView(),
	\OC::$server->getL10N('files')
);
try {
	$result = $files->rename(
		isset($_GET['dir']) ? (string)$_GET['dir'] : '',
		isset($_GET['file']) ? (string)$_GET['file'] : '',
		isset($_GET['newname']) ? (string)$_GET['newname'] : ''
	);
} catch (\Exception $e) {
	$result = [
		'success' => false,
		'data' => [
			'message' => $e->getMessage()
		]
	];
}

if($result['success'] === true){
	OCP\JSON::success(['data' => $result['data']]);
} else {
	OCP\JSON::error(['data' => $result['data']]);
}
