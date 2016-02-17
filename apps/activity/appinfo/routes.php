<?php

namespace OCA\Activity\AppInfo;

use OCP\API;

// Register an OCS API call
API::register(
	'get',
	'/cloud/activity',
	array('OCA\Activity\Api', 'get'),
	'activity'
);

$application = new Application();
$application->registerRoutes($this, ['routes' => [
	['name' => 'Settings#personal', 'url' => '/settings', 'verb' => 'POST'],
	['name' => 'Settings#feed', 'url' => '/settings/feed', 'verb' => 'POST'],
	['name' => 'Activities#showList', 'url' => '/', 'verb' => 'GET'],
	['name' => 'Activities#fetch', 'url' => '/activities/fetch', 'verb' => 'GET'],
	['name' => 'Feed#show', 'url' => '/rss.php', 'verb' => 'GET'],
]]);
