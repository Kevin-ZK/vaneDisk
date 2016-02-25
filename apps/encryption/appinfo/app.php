<?php

namespace OCA\Encryption\AppInfo;

\OCP\Util::addscript('encryption', 'encryption');

$encryptionSystemReady = \OC::$server->getEncryptionManager()->isReady();

$app = new Application([], $encryptionSystemReady);
if ($encryptionSystemReady) {
	$app->registerEncryptionModule();
	$app->registerHooks();
	$app->registerSettings();
}
