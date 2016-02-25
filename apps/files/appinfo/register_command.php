<?php

$application->add(new OCA\Files\Command\Scan(OC_User::getManager()));
$application->add(new OCA\Files\Command\DeleteOrphanedFiles(\OC::$server->getDatabaseConnection()));
