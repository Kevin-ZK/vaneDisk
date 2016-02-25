<?php

OC_JSON::checkSubAdminUser();
OCP\JSON::callCheck();

$username = isset($_POST["username"]) ? (string)$_POST["username"] : '';

if(($username === '' && !OC_User::isAdminUser(OC_User::getUser()))
	|| (!OC_User::isAdminUser(OC_User::getUser())
		&& !OC_SubAdmin::isUserAccessible(OC_User::getUser(), $username))) {
	$l = \OC::$server->getL10N('core');
	OC_JSON::error(array( 'data' => array( 'message' => $l->t('Authentication error') )));
	exit();
}

//make sure the quota is in the expected format
$quota= (string)$_POST["quota"];
if($quota !== 'none' and $quota !== 'default') {
	$quota= OC_Helper::computerFileSize($quota);
	$quota=OC_Helper::humanFileSize($quota);
}

// Return Success story
if($username) {
	\OC::$server->getConfig()->setUserValue($username, 'files', 'quota', $quota);
}else{//set the default quota when no username is specified
	if($quota === 'default') {//'default' as default quota makes no sense
		$quota='none';
	}
	OC_Appconfig::setValue('files', 'default_quota', $quota);
}
OC_JSON::success(array("data" => array( "username" => $username , 'quota' => $quota)));

