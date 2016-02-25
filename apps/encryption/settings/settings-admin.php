<?php

\OC_Util::checkAdminUser();

$tmpl = new OCP\Template('encryption', 'settings-admin');

// Check if an adminRecovery account is enabled for recovering files after lost pwd
$recoveryAdminEnabled = \OC::$server->getConfig()->getAppValue('encryption', 'recoveryAdminEnabled', '0');
$session = new \OCA\Encryption\Session(\OC::$server->getSession());


$tmpl->assign('recoveryEnabled', $recoveryAdminEnabled);
$tmpl->assign('initStatus', $session->getStatus());

return $tmpl->fetchPage();
