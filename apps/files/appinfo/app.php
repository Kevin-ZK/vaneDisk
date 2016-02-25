<?php
\OCP\App::registerAdmin('files', 'admin');

\OC::$server->getNavigationManager()->add(function () {
	$l = \OC::$server->getL10N('files');
	return [
		'id' => 'files_index',
		'order' => 0,
		'href' => \OCP\Util::linkTo('files', 'index.php'),
		'icon' => \OCP\Util::imagePath('core', 'places/files.svg'),
		'name' => $l->t('Files'),
	];
});

\OC::$server->getSearch()->registerProvider('OC\Search\Provider\File', array('apps' => array('files')));

$templateManager = \OC_Helper::getFileTemplateManager();
$templateManager->registerTemplate('text/html', 'core/templates/filetemplates/template.html');
$templateManager->registerTemplate('application/vnd.oasis.opendocument.presentation', 'core/templates/filetemplates/template.odp');
$templateManager->registerTemplate('application/vnd.oasis.opendocument.text', 'core/templates/filetemplates/template.odt');
$templateManager->registerTemplate('application/vnd.oasis.opendocument.spreadsheet', 'core/templates/filetemplates/template.ods');

\OCA\Files\App::getNavigationManager()->add(function () {
	$l = \OC::$server->getL10N('files');
	return [
		'id' => 'files',
		'appname' => 'files',
		'script' => 'list.php',
		'order' => 0,
		'name' => $l->t('All files'),
	];
});

\OC::$server->getActivityManager()->registerExtension(function() {
	return new \OCA\Files\Activity(
		\OC::$server->query('L10NFactory'),
		\OC::$server->getURLGenerator(),
		\OC::$server->getActivityManager(),
		new \OCA\Files\ActivityHelper(
			\OC::$server->getTagManager()
		),
		\OC::$server->getConfig()
	);
});
