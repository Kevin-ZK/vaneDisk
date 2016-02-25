<?php

namespace OCA\Files_Texteditor\AppInfo;

$app = new Application();

$app->registerRoutes($this, array('routes' => array(

	[
		'name' => 'FileHandling#load',
		'url' => '/ajax/loadfile',
		'verb' => 'GET'
	],
	[
		'name' => 'FileHandling#save',
		'url' => '/ajax/savefile',
		'verb' => 'PUT'
	]
)));
