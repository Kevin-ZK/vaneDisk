<?php

OC_Util::checkLoggedIn();

// Load the files we need
OC_Util::addStyle( "settings", "settings" );
OC_App::setActiveNavigationEntry( "help" );


if(isset($_GET['mode']) and $_GET['mode'] === 'admin') {
	$url=OC_Helper::linkToAbsolute( 'core', 'doc/admin/index.html' );
	$style1='';
	$style2=' active';
}else{
	$url=OC_Helper::linkToAbsolute( 'core', 'doc/user/index.html' );
	$style1=' active';
	$style2='';
}

$url1=OC_Helper::linkToRoute( "settings_help" ).'?mode=user';
$url2=OC_Helper::linkToRoute( "settings_help" ).'?mode=admin';

$tmpl = new OC_Template( "settings", "help", "user" );
$tmpl->assign( "admin", OC_User::isAdminUser(OC_User::getUser()));
$tmpl->assign( "url", $url );
$tmpl->assign( "url1", $url1 );
$tmpl->assign( "url2", $url2 );
$tmpl->assign( "style1", $style1 );
$tmpl->assign( "style2", $style2 );
$tmpl->printPage();
