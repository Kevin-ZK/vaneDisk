<?php

$app = new \OCA\TemplateEditor\App\TemplateEditor();

$app->registerRoutes($this, array('routes' => array(

	// mailTemplate settings
	array('name' => 'admin_settings#renderTemplate', 'url' => '/settings/mailtemplate', 'verb' => 'GET'),

	array('name' => 'admin_settings#updateTemplate', 'url' => '/settings/mailtemplate', 'verb' => 'POST'),

	array('name' => 'admin_settings#resetTemplate', 'url' => '/settings/mailtemplate', 'verb' => 'DELETE')

)));
