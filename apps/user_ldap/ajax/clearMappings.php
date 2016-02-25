<?php

use OCA\User_LDAP\Mapping\UserMapping;
use OCA\User_LDAP\Mapping\GroupMapping;

// Check user and app status
OCP\JSON::checkAdminUser();
OCP\JSON::checkAppEnabled('user_ldap');
OCP\JSON::callCheck();

$subject = (string)$_POST['ldap_clear_mapping'];
$mapping = null;
if($subject === 'user') {
	$mapping = new UserMapping(\OC::$server->getDatabaseConnection());
} else if($subject === 'group') {
	$mapping = new GroupMapping(\OC::$server->getDatabaseConnection());
}
try {
	if(is_null($mapping) || !$mapping->clear()) {
		$l = \OC::$server->getL10N('user_ldap');
		throw new \Exception($l->t('Failed to clear the mappings.'));
	}
	OCP\JSON::success();
} catch (\Exception $e) {
	OCP\JSON::error(array('message' => $e->getMessage()));
}
