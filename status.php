<?php

try {

	require_once 'lib/base.php';

	$systemConfig = \OC::$server->getSystemConfig();

	$installed = $systemConfig->getValue('installed') == 1;
	$maintenance = $systemConfig->getValue('maintenance', false);
	$values=array(
		'installed'=>$installed,
		'maintenance' => $maintenance,
		'version'=>implode('.', OC_Util::getVersion()),
		'versionstring'=>OC_Util::getVersionString(),
		'edition'=>OC_Util::getEditionString());
	if (OC::$CLI) {
		print_r($values);
	} else {
		header('Access-Control-Allow-Origin: *');
		header('Content-Type: application/json');
		echo json_encode($values);
	}

} catch (Exception $ex) {
	OC_Response::setStatus(OC_Response::STATUS_INTERNAL_SERVER_ERROR);
	\OCP\Util::writeLog('remote', $ex->getMessage(), \OCP\Util::FATAL);
}
