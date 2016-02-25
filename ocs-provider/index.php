<?php

require_once('../lib/base.php');
require_once(__DIR__ . '/provider.php');

header('Content-Type: application/json');

$server = \OC::$server;

$controller = new Provider(
	'ocs_provider',
	$server->getRequest(),
	$server->getAppManager()
);
echo $controller->buildProviderList()->render();
