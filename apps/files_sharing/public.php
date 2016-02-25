<?php

// This file is just used to redirect the legacy sharing URLs to the new ones

$urlGenerator = \OC::$server->getURLGenerator();
$token = isset($_GET['t']) ? $_GET['t'] : '';
$route = isset($_GET['download']) ? 'files_sharing.sharecontroller.downloadShare' : 'files_sharing.sharecontroller.showShare';

if($token !== '') {
	OC_Response::redirect($urlGenerator->linkToRoute($route, array('token' => $token)));
} else {
	header('HTTP/1.0 404 Not Found');
	$tmpl = new OCP\Template('', '404', 'guest');
	print_unescaped($tmpl->fetchPage());
}
