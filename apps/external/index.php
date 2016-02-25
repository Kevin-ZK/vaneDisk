<?php

use OCA\External\External;

OCP\JSON::checkAppEnabled('external');
OCP\User::checkLoggedIn();
OCP\Util::addStyle( 'external', 'style');

$id = isset($_GET['id']) ? (int)$_GET['id'] : 1;

$sites = External::getSites();
if (sizeof($sites) >= $id) {
	$url = $sites[$id - 1][1];
	OCP\App::setActiveNavigationEntry('external_index' . $id);

	$tmpl = new OCP\Template('external', 'frame', 'user');
	//overwrite x-frame-options
	header('X-Frame-Options: ALLOW-FROM *');

	$tmpl->assign('url', $url);
	$tmpl->printPage();
} else {
	\OC_Util::redirectToDefaultPage();
}

