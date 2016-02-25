<?php

use OCA\Encryption\Command\MigrateKeys;

$userManager = OC::$server->getUserManager();
$view = new \OC\Files\View();
$config = \OC::$server->getConfig();
$connection = \OC::$server->getDatabaseConnection();
$logger = \OC::$server->getLogger();
$application->add(new MigrateKeys($userManager, $view, $connection, $config, $logger));
