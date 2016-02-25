<?php

// Check user and app status
OCP\JSON::checkAdminUser();
OCP\JSON::checkAppEnabled('user_ldap');
OCP\JSON::callCheck();

$prefix = (string)$_POST['ldap_serverconfig_chooser'];
$helper = new \OCA\user_ldap\lib\Helper();
if($helper->deleteServerConfiguration($prefix)) {
	OCP\JSON::success();
} else {
	$l = \OC::$server->getL10N('user_ldap');
	OCP\JSON::error(array('message' => $l->t('Failed to delete the server configuration')));
}
