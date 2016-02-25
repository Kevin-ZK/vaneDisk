<?php

/** @var $this \OCP\Route\IRouter */
$this->create('user_ldap_ajax_clearMappings', 'ajax/clearMappings.php')
	->actionInclude('user_ldap/ajax/clearMappings.php');
$this->create('user_ldap_ajax_deleteConfiguration', 'ajax/deleteConfiguration.php')
	->actionInclude('user_ldap/ajax/deleteConfiguration.php');
$this->create('user_ldap_ajax_getConfiguration', 'ajax/getConfiguration.php')
	->actionInclude('user_ldap/ajax/getConfiguration.php');
$this->create('user_ldap_ajax_getNewServerConfigPrefix', 'ajax/getNewServerConfigPrefix.php')
	->actionInclude('user_ldap/ajax/getNewServerConfigPrefix.php');
$this->create('user_ldap_ajax_setConfiguration', 'ajax/setConfiguration.php')
	->actionInclude('user_ldap/ajax/setConfiguration.php');
$this->create('user_ldap_ajax_testConfiguration', 'ajax/testConfiguration.php')
	->actionInclude('user_ldap/ajax/testConfiguration.php');
$this->create('user_ldap_ajax_wizard', 'ajax/wizard.php')
	->actionInclude('user_ldap/ajax/wizard.php');
