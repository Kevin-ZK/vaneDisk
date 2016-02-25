<?php
// in case there are private configurations in the users home -> use them
$privateConfigFile = $_SERVER['HOME'] . '/owncloud-extfs-test-config.php';
if (file_exists($privateConfigFile)) {
	$config = include($privateConfigFile);
	return $config;
}

// this is now more a template now for your private configurations
return array(
	'ftp'=>array(
		'run'=>false,
		'host'=>'localhost',
		'user'=>'test',
		'password'=>'test',
		'root'=>'/test',
	),
	'webdav'=>array(
		'run'=>false,
		'host'=>'localhost',
		'user'=>'test',
		'password'=>'test',
		'root'=>'',
		// wait delay in seconds after write operations
		// (only in tests)
		// set to higher value for lighttpd webdav
		'wait'=> 0
	),
	'owncloud'=>array(
		'run'=>false,
		'host'=>'localhost/owncloud',
		'user'=>'test',
		'password'=>'test',
		'root'=>'',
	),
	'google'=>array(
		'run'=> false,
		'configured' => 'true',
		'client_id' => '',
		'client_secret' => '',
		'token' => '',
	),
	'swift' => array(
		'run' => false,
		'user' => 'test',
		'bucket' => 'test',
		'region' => 'DFW',
		'key' => 'test', //to be used only with Rackspace Cloud Files
		//'tenant' => 'test', //to be used only with OpenStack Object Storage
		//'password' => 'test', //to be use only with OpenStack Object Storage
		//'service_name' => 'swift', //should be 'swift' for OpenStack Object Storage and 'cloudFiles' for Rackspace Cloud Files (default value)
		//'url' => 'https://identity.api.rackspacecloud.com/v2.0/', //to be used with Rackspace Cloud Files and OpenStack Object Storage
		//'timeout' => 5 // timeout of HTTP requests in seconds
	),
	'smb'=>array(
		'run'=>false,
		'user'=>'test',
		'password'=>'test',
		'host'=>'localhost',
		'share'=>'/test',
		'root'=>'/test/',
	),
	'amazons3'=>array(
		'run'=>false,
		'key'=>'test',
		'secret'=>'test',
		'bucket'=>'bucket'
		//'hostname' => 'your.host.name',
		//'port' => '443',
		//'use_ssl' => 'true',
		//'region' => 'eu-west-1',
		//'test'=>'true',
		//'timeout'=>20
	),
	'dropbox' => array (
		'run'=>false,
		'root'=>'owncloud',
		'configured' => 'true',
		'app_key' => '',
		'app_secret' => '',
		'token' => '',
		'token_secret' => ''
	),
	'sftp' => array (
		'run'=>false,
		'host'=>'localhost',
		'user'=>'test',
		'password'=>'test',
		'root'=>'/test'
	),
	'sftp_key' => array (
                'run'=>false,
                'host'=>'localhost',
                'user'=>'test',
                'public_key'=>'ssh-rsa AAAAB3NzaC1yc2EAAAADAQABAAAAgQDJPTvz3OLonF2KSGEKP/nd4CPmRYvemG2T4rIiNYjDj0U5y+2sKEWbjiUlQl2bsqYuVoJ+/UNJlGQbbZ08kQirFeo1GoWBzqioaTjUJfbLN6TzVVKXxR9YIVmH7Ajg2iEeGCndGgbmnPfj+kF9TR9IH8vMVvtubQwf7uEwB0ALhw== phpseclib-generated-key',
		'private_key'=>'test',
                'root'=>'/test'
        ),
);
