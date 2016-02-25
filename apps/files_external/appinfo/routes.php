<?php

namespace OCA\Files_External\Appinfo;

/**
 * @var $this \OC\Route\Router
 **/
$application = new Application();
$application->registerRoutes(
	$this,
	array(
		'resources' => array(
			'global_storages' => array('url' => '/globalstorages'),
			'user_storages' => array('url' => '/userstorages'),
		),
		'routes' => array(
			array(
				'name' => 'Ajax#getSshKeys',
				'url' => '/ajax/sftp_key.php',
				'verb' => 'POST',
				'requirements' => array()
			)
		)
	)
);

$this->create('files_external_dropbox', 'ajax/dropbox.php')
	->actionInclude('files_external/ajax/dropbox.php');
$this->create('files_external_google', 'ajax/google.php')
	->actionInclude('files_external/ajax/google.php');


$this->create('files_external_list_applicable', '/applicable')
	->actionInclude('files_external/ajax/applicable.php');

\OCP\API::register('get',
		'/apps/files_external/api/v1/mounts',
		array('\OCA\Files\External\Api', 'getUserMounts'),
		'files_external');

