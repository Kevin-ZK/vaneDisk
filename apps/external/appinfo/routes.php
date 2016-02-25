<?php

/** @var $this \OCP\Route\IRouter */
$this->create('external_index', '/{id}')
	->actionInclude('external/index.php');
$this->create('external_ajax_setsites', 'ajax/setsites.php')
	->actionInclude('external/ajax/setsites.php');
