<?php

OC_JSON::callCheck();
OC_JSON::checkSubAdminUser();

$userCount = 0;

$currentUser = \OC::$server->getUserSession()->getUser()->getUID();

if (!OC_User::isAdminUser($currentUser)) {
	$groups = OC_SubAdmin::getSubAdminsGroups($currentUser);

	foreach ($groups as $group) {
		$userCount += count(OC_Group::usersInGroup($group));

	}
} else {

	$userCountArray = \OC::$server->getUserManager()->countUsers();

	if (!empty($userCountArray)) {
		foreach ($userCountArray as $classname => $usercount) {
			$userCount += $usercount;
		}
	}
}


OC_JSON::success(array('count' => $userCount));
