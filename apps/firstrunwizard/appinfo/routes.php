<?php

/** @var $this OC\Route\Router */

$this->create('firstrunwizard_enable', 'ajax/enable.php')
	->actionInclude('firstrunwizard/ajax/enable.php');
$this->create('firstrunwizard_disable', 'ajax/disable.php')
	->actionInclude('firstrunwizard/ajax/disable.php');
$this->create('firstrunwizard_wizard', 'wizard.php')
	->actionInclude('firstrunwizard/wizard.php');

