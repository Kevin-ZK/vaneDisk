<?php

\OC_Util::checkLoggedIn();

$l = \OC::$server->getL10N('files_sharing');

$isIE8 = false;
preg_match('/MSIE (.*?);/', $_SERVER['HTTP_USER_AGENT'], $matches);
if (count($matches) > 0 && $matches[1] <= 9) {
	$isIE8 = true;
}

$uid = \OC::$server->getUserSession()->getUser()->getUID();
$server = \OC::$server->getURLGenerator()->getAbsoluteURL('/');
$cloudID = $uid . '@' . rtrim(\OCA\Files_Sharing\Helper::removeProtocolFromUrl($server), '/');
$url = 'https://owncloud.org/federation#' . $cloudID;
$ownCloudLogoPath = \OC::$server->getURLGenerator()->imagePath('core', 'logo-icon.svg');

$tmpl = new OCP\Template('files_sharing', 'settings-personal');
$tmpl->assign('outgoingServer2serverShareEnabled', \OCA\Files_Sharing\Helper::isOutgoingServer2serverShareEnabled());
$tmpl->assign('message_with_URL', $l->t('Share with me through my #ownCloud Federated Cloud ID, see %s', [$url]));
$tmpl->assign('message_without_URL', $l->t('Share with me through my #ownCloud Federated Cloud ID', [$cloudID]));
$tmpl->assign('owncloud_logo_path', $ownCloudLogoPath);
$tmpl->assign('reference', $url);
$tmpl->assign('cloudId', $cloudID);
$tmpl->assign('showShareIT', !$isIE8);

return $tmpl->fetchPage();
