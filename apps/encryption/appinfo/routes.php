<?php

namespace OCA\Encryption\AppInfo;

(new Application())->registerRoutes($this, array('routes' => array(

	[
		'name' => 'Recovery#adminRecovery',
		'url' => '/ajax/adminRecovery',
		'verb' => 'POST'
	],
	[
		'name' => 'Settings#updatePrivateKeyPassword',
		'url' => '/ajax/updatePrivateKeyPassword',
		'verb' => 'POST'
	],
	[
		'name' => 'Recovery#changeRecoveryPassword',
		'url' => '/ajax/changeRecoveryPassword',
		'verb' => 'POST'
	],
	[
		'name' => 'Recovery#userSetRecovery',
		'url' => '/ajax/userSetRecovery',
		'verb' => 'POST'
	],
	[
		'name' => 'Status#getStatus',
		'url' => '/ajax/getStatus',
		'verb' => 'GET'
	]


)));
