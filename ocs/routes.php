<?php

use OCP\API;

// Config
API::register(
	'get',
	'/config',
	array('OC_OCS_Config', 'apiConfig'),
	'core',
	API::GUEST_AUTH
	);
// Person
API::register(
	'post',
	'/person/check',
	array('OC_OCS_Person', 'check'),
	'core',
	API::GUEST_AUTH
	);
// Privatedata
API::register(
	'get',
	'/privatedata/getattribute',
	array('OC_OCS_Privatedata', 'get'),
	'core',
	API::USER_AUTH,
	array('app' => '', 'key' => '')
	);
API::register(
	'get',
	'/privatedata/getattribute/{app}',
	array('OC_OCS_Privatedata', 'get'),
	'core',
	API::USER_AUTH,
	array('key' => '')
	);
API::register(
	'get',
	'/privatedata/getattribute/{app}/{key}',
	array('OC_OCS_Privatedata', 'get'),
	'core',
	API::USER_AUTH
	);
API::register(
	'post',
	'/privatedata/setattribute/{app}/{key}',
	array('OC_OCS_Privatedata', 'set'),
	'core',
	API::USER_AUTH
	);
API::register(
	'post',
	'/privatedata/deleteattribute/{app}/{key}',
	array('OC_OCS_Privatedata', 'delete'),
	'core',
	API::USER_AUTH
	);
// cloud
API::register(
	'get',
	'/cloud/capabilities',
	array('OC_OCS_Cloud', 'getCapabilities'),
	'core',
	API::USER_AUTH
	);
API::register(
	'get',
	'/cloud/users/{userid}',
	array('OC_OCS_Cloud', 'getUser'),
	'core',
	API::USER_AUTH
);
API::register(
	'get',
	'/cloud/user',
	array('OC_OCS_Cloud', 'getCurrentUser'),
	'core',
	API::USER_AUTH
);

// Server-to-Server Sharing
$s2s = new \OCA\Files_Sharing\API\Server2Server();
API::register('post',
		'/cloud/shares',
		array($s2s, 'createShare'),
		'files_sharing',
		API::GUEST_AUTH
);

API::register('post',
		'/cloud/shares/{id}/accept',
		array($s2s, 'acceptShare'),
		'files_sharing',
		API::GUEST_AUTH
);

API::register('post',
		'/cloud/shares/{id}/decline',
		array($s2s, 'declineShare'),
		'files_sharing',
		API::GUEST_AUTH
);

API::register('post',
		'/cloud/shares/{id}/unshare',
		array($s2s, 'unshare'),
		'files_sharing',
		API::GUEST_AUTH
);
