<?php

require_once '../lib/base.php';

if (\OCP\Util::needUpgrade()
	|| \OC::$server->getSystemConfig()->getValue('maintenance', false)
	|| \OC::$server->getSystemConfig()->getValue('singleuser', false)) {
	// since the behavior of apps or remotes are unpredictable during
	// an upgrade, return a 503 directly
	OC_Response::setStatus(OC_Response::STATUS_SERVICE_UNAVAILABLE);
	$response = new OC_OCS_Result(null, OC_Response::STATUS_SERVICE_UNAVAILABLE, 'Service unavailable');
	OC_API::respond($response, OC_API::requestedFormat());
	exit;
}

use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\Exception\MethodNotAllowedException;

try {
	// load all apps to get all api routes properly setup
	OC_App::loadApps();

	// force language as given in the http request
	\OC_L10N::setLanguageFromRequest();

	OC::$server->getRouter()->match('/ocs'.\OC::$server->getRequest()->getRawPathInfo());
} catch (ResourceNotFoundException $e) {
	OC_API::setContentType();
	OC_OCS::notFound();
} catch (MethodNotAllowedException $e) {
	OC_API::setContentType();
	OC_Response::setStatus(405);
}

