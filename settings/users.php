<?php

OC_Util::checkSubAdminUser();

OC_App::setActiveNavigationEntry( 'core_users' );

$userManager = \OC_User::getManager();
$groupManager = \OC_Group::getManager();

// Set the sort option: SORT_USERCOUNT or SORT_GROUPNAME
$sortGroupsBy = \OC\Group\MetaData::SORT_USERCOUNT;

if (class_exists('\OCA\user_ldap\GROUP_LDAP')) {
	$isLDAPUsed =
		   $groupManager->isBackendUsed('\OCA\user_ldap\GROUP_LDAP')
		|| $groupManager->isBackendUsed('\OCA\user_ldap\Group_Proxy');
	if ($isLDAPUsed) {
		// LDAP user count can be slow, so we sort by group name here
		$sortGroupsBy = \OC\Group\MetaData::SORT_GROUPNAME;
	}
}

$config = \OC::$server->getConfig();

$isAdmin = OC_User::isAdminUser(OC_User::getUser());

$groupsInfo = new \OC\Group\MetaData(OC_User::getUser(), $isAdmin, $groupManager);
$groupsInfo->setSorting($sortGroupsBy);
list($adminGroup, $groups) = $groupsInfo->get();

$recoveryAdminEnabled = OC_App::isEnabled('encryption') &&
					    $config->getAppValue( 'encryption', 'recoveryAdminEnabled', null );

if($isAdmin) {
	$subadmins = OC_SubAdmin::getAllSubAdmins();
}else{
	/* Retrieve group IDs from $groups array, so we can pass that information into OC_Group::displayNamesInGroups() */
	$gids = array();
	foreach($groups as $group) {
		if (isset($group['id'])) {
			$gids[] = $group['id'];
		}
	}
	$subadmins = false;
}

// load preset quotas
$quotaPreset=$config->getAppValue('files', 'quota_preset', '1 GB, 5 GB, 10 GB');
$quotaPreset=explode(',', $quotaPreset);
foreach($quotaPreset as &$preset) {
	$preset=trim($preset);
}
$quotaPreset=array_diff($quotaPreset, array('default', 'none'));

$defaultQuota=$config->getAppValue('files', 'default_quota', 'none');
$defaultQuotaIsUserDefined=array_search($defaultQuota, $quotaPreset)===false
	&& array_search($defaultQuota, array('none', 'default'))===false;

$tmpl = new OC_Template("settings", "users/main", "user");
$tmpl->assign('groups', $groups);
$tmpl->assign('sortGroups', $sortGroupsBy);
$tmpl->assign('adminGroup', $adminGroup);
$tmpl->assign('isAdmin', (int)$isAdmin);
$tmpl->assign('subadmins', $subadmins);
$tmpl->assign('numofgroups', count($groups) + count($adminGroup));
$tmpl->assign('quota_preset', $quotaPreset);
$tmpl->assign('default_quota', $defaultQuota);
$tmpl->assign('defaultQuotaIsUserDefined', $defaultQuotaIsUserDefined);
$tmpl->assign('recoveryAdminEnabled', $recoveryAdminEnabled);
$tmpl->assign('enableAvatars', \OC::$server->getConfig()->getSystemValue('enable_avatars', true));
$tmpl->printPage();
