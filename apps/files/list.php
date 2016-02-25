<?php

// Check if we are a user
OCP\User::checkLoggedIn();

$config = \OC::$server->getConfig();
// TODO: move this to the generated config.js
$publicUploadEnabled = $config->getAppValue('core', 'shareapi_allow_public_upload', 'yes');
$uploadLimit=OCP\Util::uploadLimit();

// renders the controls and table headers template
$tmpl = new OCP\Template('files', 'list', '');
$tmpl->assign('uploadLimit', $uploadLimit); // PHP upload limit
$tmpl->assign('publicUploadEnabled', $publicUploadEnabled);
$tmpl->printPage();

