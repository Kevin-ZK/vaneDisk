<?php

// Check user and app status
OCP\JSON::checkAdminUser();
OCP\JSON::checkAppEnabled('user_ldap');
OCP\JSON::callCheck();

$prefix = (string)$_POST['ldap_serverconfig_chooser'];
$ldapWrapper = new OCA\user_ldap\lib\LDAP();
$connection = new \OCA\user_ldap\lib\Connection($ldapWrapper, $prefix);
OCP\JSON::success(array('configuration' => $connection->getConfiguration()));
