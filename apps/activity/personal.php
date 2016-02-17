<?php


$app = new \OCA\Activity\AppInfo\Application();
/** @var OCA\Activity\Controller\Settings $controller */
$controller = $app->getContainer()->query('SettingsController');
return $controller->displayPanel()->render();
