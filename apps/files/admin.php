<?php

OCP\User::checkAdminUser();

$htaccessWorking=(getenv('htaccessWorking')=='true');

$upload_max_filesize = OCP\Util::computerFileSize(ini_get('upload_max_filesize'));
$post_max_size = OCP\Util::computerFileSize(ini_get('post_max_size'));
$maxUploadFilesize = OCP\Util::humanFileSize(min($upload_max_filesize, $post_max_size));
if($_POST && OC_Util::isCallRegistered()) {
	if(isset($_POST['maxUploadSize'])) {
		if(($setMaxSize = OC_Files::setUploadLimit(OCP\Util::computerFileSize($_POST['maxUploadSize']))) !== false) {
			$maxUploadFilesize = OCP\Util::humanFileSize($setMaxSize);
		}
	}
}

$htaccessWritable=is_writable(OC::$SERVERROOT.'/.htaccess');

$tmpl = new OCP\Template( 'files', 'admin' );
$tmpl->assign( 'uploadChangable', $htaccessWorking and $htaccessWritable );
$tmpl->assign( 'uploadMaxFilesize', $maxUploadFilesize);
// max possible makes only sense on a 32 bit system
$tmpl->assign( 'displayMaxPossibleUploadSize', PHP_INT_SIZE===4);
$tmpl->assign( 'maxPossibleUploadSize', OCP\Util::humanFileSize(PHP_INT_MAX));
return $tmpl->fetchPage();
