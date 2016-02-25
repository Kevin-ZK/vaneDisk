<?php


// Check if we are a user
OCP\User::checkLoggedIn();

$defaults = new \OCP\Defaults();

//links to clients
$clients = array(
	'desktop' => OCP\Config::getSystemValue('customclient_desktop', $defaults->getSyncClientUrl()),
	'android' => OCP\Config::getSystemValue('customclient_android', $defaults->getAndroidClientUrl()),
	'ios'     => OCP\Config::getSystemValue('customclient_ios', $defaults->getiOSClientUrl())
);

$tmpl = new OCP\Template( 'firstrunwizard', 'wizard', '' );
$tmpl->assign('logo', OCP\Util::linkTo('core','img/logo-inverted.svg'));
$tmpl->assign('clients', $clients);
$tmpl->printPage();

