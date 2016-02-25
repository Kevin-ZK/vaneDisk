<?php

OC_Util::checkAdminUser();

if($_POST) {
	// CSRF check
	OCP\JSON::callCheck();

	if(isset($_POST['webdav_url'])) {
		OC_CONFIG::setValue('user_webdavauth_url', strip_tags($_POST['webdav_url']));
	}
}

// fill template
$tmpl = new OC_Template( 'user_webdavauth', 'settings');
$tmpl->assign( 'webdav_url', OC_Config::getValue( "user_webdavauth_url" ));

return $tmpl->fetchPage();
