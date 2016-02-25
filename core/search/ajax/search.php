<?php

// Check if we are a user
\OCP\JSON::checkLoggedIn();
\OCP\JSON::callCheck();
\OC::$server->getSession()->close();

if (isset($_GET['query'])) {
	$query = $_GET['query'];
} else {
	$query = '';
}
if (isset($_GET['inApps'])) {
	$inApps = $_GET['inApps'];
	if (is_string($inApps)) {
		$inApps = array($inApps);
	}
} else {
	$inApps = array();
}
if (isset($_GET['page'])) {
	$page = (int)$_GET['page'];
} else {
	$page = 1;
}
if (isset($_GET['size'])) {
	$size = (int)$_GET['size'];
} else {
	$size = 30;
}
if($query) {
	$result = \OC::$server->getSearch()->searchPaged($query, $inApps, $page, $size);
	OC_JSON::encodedPrint($result);
}
else {
	echo 'false';
}
