<?php

require_once OC_App::getAppPath('user_webdavauth').'/user_webdavauth.php';

OC_APP::registerAdmin('user_webdavauth', 'settings');

OC_User::registerBackend("WEBDAVAUTH");
OC_User::useBackend( "WEBDAVAUTH" );

// add settings page to navigation
$entry = array(
	'id' => "user_webdavauth_settings",
	'order'=>1,
	'href' => \OCP\Util::linkTo( "user_webdavauth", "settings.php" ),
	'name' => 'WEBDAVAUTH'
);
