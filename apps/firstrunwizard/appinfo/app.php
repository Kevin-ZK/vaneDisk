<?php

OC::$CLASSPATH['OCA_FirstRunWizard\Config'] = 'firstrunwizard/lib/firstrunwizard.php';

OCP\Util::addStyle( 'firstrunwizard', 'colorbox');
OCP\Util::addScript( 'firstrunwizard', 'jquery.colorbox');
OCP\Util::addScript( 'firstrunwizard', 'firstrunwizard');

OCP\Util::addStyle('firstrunwizard', 'firstrunwizard');

if(\OCP\User::isLoggedIn() and \OCA_FirstRunWizard\Config::isenabled()){
	OCP\Util::addScript( 'firstrunwizard', 'activate');
}
