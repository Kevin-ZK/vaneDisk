<?php
/**
 *
 * @copyright Copyright (c) 2015, ownCloud, Inc.
 * @license AGPL-3.0
 *
 * This code is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License, version 3,
 * as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License, version 3,
 * along with this program.  If not, see <http://www.gnu.org/licenses/>
 *
 */

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
