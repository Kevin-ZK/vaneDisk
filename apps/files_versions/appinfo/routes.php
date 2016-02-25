<?php

/** @var $this \OCP\Route\IRouter */
$this->create('core_ajax_versions_preview', '/preview')->action(
function() {
	require_once __DIR__ . '/../ajax/preview.php';
});

$this->create('files_versions_download', 'download.php')
	->actionInclude('files_versions/download.php');
$this->create('files_versions_ajax_getVersions', 'ajax/getVersions.php')
	->actionInclude('files_versions/ajax/getVersions.php');
$this->create('files_versions_ajax_rollbackVersion', 'ajax/rollbackVersion.php')
	->actionInclude('files_versions/ajax/rollbackVersion.php');

// Register with the capabilities API
\OCP\API::register('get', '/cloud/capabilities', array('OCA\Files_Versions\Capabilities', 'getCapabilities'), 'files_versions', \OCP\API::USER_AUTH);
