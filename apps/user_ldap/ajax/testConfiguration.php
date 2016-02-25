<?php

// Check user and app status
OCP\JSON::checkAdminUser();
OCP\JSON::checkAppEnabled('user_ldap');
OCP\JSON::callCheck();

$l = \OC::$server->getL10N('user_ldap');

$ldapWrapper = new OCA\user_ldap\lib\LDAP();
$connection = new \OCA\user_ldap\lib\Connection($ldapWrapper, '', null);
//needs to be true, otherwise it will also fail with an irritating message
$_POST['ldap_configuration_active'] = 1;

try {
	if ($connection->setConfiguration($_POST)) {
		//Configuration is okay
		if ($connection->bind()) {
			/*
			 * This shiny if block is an ugly hack to find out whether anonymous
			 * bind is possible on AD or not. Because AD happily and constantly
			 * replies with success to any anonymous bind request, we need to
			 * fire up a broken operation. If AD does not allow anonymous bind,
			 * it will end up with LDAP error code 1 which is turned into an
			 * exception by the LDAP wrapper. We catch this. Other cases may
			 * pass (like e.g. expected syntax error).
			 */
			try {
				$ldapWrapper->read($connection->getConnectionResource(), 'neverwhere', 'objectClass=*', array('dn'));
			} catch (\Exception $e) {
				if($e->getCode() === 1) {
					OCP\JSON::error(array('message' => $l->t('The configuration is invalid: anonymous bind is not allowed.')));
					exit;
				}
			}
			OCP\JSON::success(array('message'
			=> $l->t('The configuration is valid and the connection could be established!')));
		} else {
			OCP\JSON::error(array('message'
			=> $l->t('The configuration is valid, but the Bind failed. Please check the server settings and credentials.')));
		}
	} else {
		OCP\JSON::error(array('message'
		=> $l->t('The configuration is invalid. Please have a look at the logs for further details.')));
	}
} catch (\Exception $e) {
	OCP\JSON::error(array('message' => $e->getMessage()));
}
