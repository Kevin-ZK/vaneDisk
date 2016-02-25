<?php

OCP\JSON::checkLoggedIn();
OCP\JSON::callCheck();
\OC::$server->getSession()->close();

$trashStatus = OCA\Files_Trashbin\Trashbin::isEmpty(OCP\User::getUser());

OCP\JSON::success(array("data" => array("isEmpty" => $trashStatus)));


