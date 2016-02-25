<?php

OCP\JSON::checkLoggedIn();
OCP\JSON::checkAppEnabled('firstrunwizard');
OCP\JSON::callCheck();

\OCA_FirstRunWizard\Config::enable();


