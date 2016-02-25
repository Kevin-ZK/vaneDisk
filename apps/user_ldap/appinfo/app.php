<?php

OCP\App::registerAdmin('user_ldap', 'settings');

$helper = new \OCA\user_ldap\lib\Helper();
$configPrefixes = $helper->getServerConfigurationPrefixes(true);
$ldapWrapper = new OCA\user_ldap\lib\LDAP();
$ocConfig = \OC::$server->getConfig();
if(count($configPrefixes) === 1) {
	$dbc = \OC::$server->getDatabaseConnection();
	$userManager = new OCA\user_ldap\lib\user\Manager($ocConfig,
		new OCA\user_ldap\lib\FilesystemHelper(),
		new OCA\user_ldap\lib\LogWrapper(),
		\OC::$server->getAvatarManager(),
		new \OCP\Image(),
		$dbc
	);
	$connector = new OCA\user_ldap\lib\Connection($ldapWrapper, $configPrefixes[0]);
	$ldapAccess = new OCA\user_ldap\lib\Access($connector, $ldapWrapper, $userManager);

	$ldapAccess->setUserMapper(new OCA\User_LDAP\Mapping\UserMapping($dbc));
	$ldapAccess->setGroupMapper(new OCA\User_LDAP\Mapping\GroupMapping($dbc));
	$userBackend  = new OCA\user_ldap\USER_LDAP($ldapAccess, $ocConfig);
	$groupBackend = new OCA\user_ldap\GROUP_LDAP($ldapAccess);
} else if(count($configPrefixes) > 1) {
	$userBackend  = new OCA\user_ldap\User_Proxy(
		$configPrefixes, $ldapWrapper, $ocConfig
	);
	$groupBackend  = new OCA\user_ldap\Group_Proxy($configPrefixes, $ldapWrapper);
}

if(count($configPrefixes) > 0) {
	// register user backend
	OC_User::useBackend($userBackend);
	OC_Group::useBackend($groupBackend);
}

OCP\Backgroundjob::registerJob('OCA\user_ldap\lib\Jobs');
OCP\Backgroundjob::registerJob('\OCA\User_LDAP\Jobs\CleanUp');

if(OCP\App::isEnabled('user_webdavauth')) {
	OCP\Util::writeLog('user_ldap',
		'user_ldap and user_webdavauth are incompatible. You may experience unexpected behaviour',
		OCP\Util::WARN);
}
