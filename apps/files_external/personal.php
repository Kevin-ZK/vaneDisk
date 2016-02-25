<?php

OCP\Util::addScript('files_external', 'settings');
OCP\Util::addStyle('files_external', 'settings');
$backends = OC_Mount_Config::getPersonalBackends();

$mounts = OC_Mount_Config::getPersonalMountPoints();
$hasId = true;
foreach ($mounts as $mount) {
	if (!isset($mount['id'])) {
		// some mount points are missing ids
		$hasId = false;
		break;
	}
}

if (!$hasId) {
	$service = new \OCA\Files_external\Service\UserStoragesService(\OC::$server->getUserSession());
	// this will trigger the new storage code which will automatically
	// generate storage config ids
	$service->getAllStorages();
	// re-read updated config
	$mounts = OC_Mount_Config::getPersonalMountPoints();
	// TODO: use the new storage config format in the template
}

$tmpl = new OCP\Template('files_external', 'settings');
$tmpl->assign('encryptionEnabled', \OC::$server->getEncryptionManager()->isEnabled());
$tmpl->assign('isAdminPage', false);
$tmpl->assign('mounts', $mounts);
$tmpl->assign('dependencies', OC_Mount_Config::checkDependencies());
$tmpl->assign('backends', $backends);
return $tmpl->fetchPage();
