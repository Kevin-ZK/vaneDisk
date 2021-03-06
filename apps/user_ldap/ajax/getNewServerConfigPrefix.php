<?php

// Check user and app status
OCP\JSON::checkAdminUser();
OCP\JSON::checkAppEnabled('user_ldap');
OCP\JSON::callCheck();

$helper = new \OCA\user_ldap\lib\Helper();
$serverConnections = $helper->getServerConfigurationPrefixes();
sort($serverConnections);
$lk = array_pop($serverConnections);
$ln = intval(str_replace('s', '', $lk));
$nk = 's'.str_pad($ln+1, 2, '0', STR_PAD_LEFT);

$resultData = array('configPrefix' => $nk);

$newConfig = new \OCA\user_ldap\lib\Configuration($nk, false);
if(isset($_POST['copyConfig'])) {
	$originalConfig = new \OCA\user_ldap\lib\Configuration($_POST['copyConfig']);
	$newConfig->setConfiguration($originalConfig->getConfiguration());
} else {
	$configuration = new \OCA\user_ldap\lib\Configuration($nk, false);
	$newConfig->setConfiguration($configuration->getDefaults());
	$resultData['defaults'] = $configuration->getDefaults();
}
$newConfig->saveConfiguration();

OCP\JSON::success($resultData);
