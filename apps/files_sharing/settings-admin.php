<?php

\OC_Util::checkAdminUser();

\OCP\Util::addScript('files_sharing', 'settings-admin');

$tmpl = new OCP\Template('files_sharing', 'settings-admin');
$tmpl->assign('outgoingServer2serverShareEnabled', OCA\Files_Sharing\Helper::isOutgoingServer2serverShareEnabled());
$tmpl->assign('incomingServer2serverShareEnabled', OCA\Files_Sharing\Helper::isIncomingServer2serverShareEnabled());

return $tmpl->fetchPage();
