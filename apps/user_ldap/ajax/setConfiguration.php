<?php

// Check user and app status
OCP\JSON::checkAdminUser();
OCP\JSON::checkAppEnabled('user_ldap');
OCP\JSON::callCheck();

$prefix = (string)$_POST['ldap_serverconfig_chooser'];

// Checkboxes are not submitted, when they are unchecked. Set them manually.
// only legacy checkboxes (Advanced and Expert tab) need to be handled here,
// the Wizard-like tabs handle it on their own
$chkboxes = array('ldap_configuration_active', 'ldap_override_main_server',
				  'ldap_nocase', 'ldap_turn_off_cert_check');
foreach($chkboxes as $boxid) {
	if(!isset($_POST[$boxid])) {
		$_POST[$boxid] = 0;
	}
}

$ldapWrapper = new OCA\user_ldap\lib\LDAP();
$connection = new \OCA\user_ldap\lib\Connection($ldapWrapper, $prefix);
$connection->setConfiguration($_POST);
$connection->saveConfiguration();
OCP\JSON::success();
