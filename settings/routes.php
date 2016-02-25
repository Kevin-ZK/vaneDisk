<?php

namespace OC\Settings;

$application = new Application();
$application->registerRoutes($this, [
	'resources' => [
		'groups' => ['url' => '/settings/users/groups'],
		'users' => ['url' => '/settings/users/users']
	],
	'routes' => [
		['name' => 'MailSettings#setMailSettings', 'url' => '/settings/admin/mailsettings', 'verb' => 'POST'],
		['name' => 'MailSettings#storeCredentials', 'url' => '/settings/admin/mailsettings/credentials', 'verb' => 'POST'],
		['name' => 'MailSettings#sendTestMail', 'url' => '/settings/admin/mailtest', 'verb' => 'POST'],
		['name' => 'Encryption#startMigration', 'url' => '/settings/admin/startmigration', 'verb' => 'POST'],
		['name' => 'AppSettings#listCategories', 'url' => '/settings/apps/categories', 'verb' => 'GET'],
		['name' => 'AppSettings#viewApps', 'url' => '/settings/apps', 'verb' => 'GET'],
		['name' => 'AppSettings#listApps', 'url' => '/settings/apps/list', 'verb' => 'GET'],
		['name' => 'AppSettings#changeExperimentalConfigState', 'url' => '/settings/apps/experimental', 'verb' => 'POST'],
		['name' => 'SecuritySettings#trustedDomains', 'url' => '/settings/admin/security/trustedDomains', 'verb' => 'POST'],
		['name' => 'Users#setMailAddress', 'url' => '/settings/users/{id}/mailAddress', 'verb' => 'PUT'],
		['name' => 'LogSettings#setLogLevel', 'url' => '/settings/admin/log/level', 'verb' => 'POST'],
		['name' => 'LogSettings#getEntries', 'url' => '/settings/admin/log/entries', 'verb' => 'GET'],
		['name' => 'LogSettings#download', 'url' => '/settings/admin/log/download', 'verb' => 'GET'],
		['name' => 'CheckSetup#check', 'url' => '/settings/ajax/checksetup', 'verb' => 'GET'],
		['name' => 'Certificate#addPersonalRootCertificate', 'url' => '/settings/personal/certificate', 'verb' => 'POST'],
		['name' => 'Certificate#removePersonalRootCertificate', 'url' => '/settings/personal/certificate/{certificateIdentifier}', 'verb' => 'DELETE'],
	]
]);

/** @var $this \OCP\Route\IRouter */

// Settings pages
$this->create('settings_help', '/settings/help')
	->actionInclude('settings/help.php');
$this->create('settings_personal', '/settings/personal')
	->actionInclude('settings/personal.php');
$this->create('settings_users', '/settings/users')
	->actionInclude('settings/users.php');
$this->create('settings_admin', '/settings/admin')
	->actionInclude('settings/admin.php');
// Settings ajax actions
// users
$this->create('settings_ajax_everyonecount', '/settings/ajax/geteveryonecount')
	->actionInclude('settings/ajax/geteveryonecount.php');
$this->create('settings_ajax_setquota', '/settings/ajax/setquota.php')
	->actionInclude('settings/ajax/setquota.php');
$this->create('settings_ajax_togglegroups', '/settings/ajax/togglegroups.php')
	->actionInclude('settings/ajax/togglegroups.php');
$this->create('settings_ajax_togglesubadmins', '/settings/ajax/togglesubadmins.php')
	->actionInclude('settings/ajax/togglesubadmins.php');
$this->create('settings_users_changepassword', '/settings/users/changepassword')
	->post()
	->action('OC\Settings\ChangePassword\Controller', 'changeUserPassword');
$this->create('settings_ajax_changedisplayname', '/settings/ajax/changedisplayname.php')
	->actionInclude('settings/ajax/changedisplayname.php');
$this->create('settings_ajax_changegorupname', '/settings/ajax/changegroupname.php')
	->actionInclude('settings/ajax/changegroupname.php');	
// personal
$this->create('settings_personal_changepassword', '/settings/personal/changepassword')
	->post()
	->action('OC\Settings\ChangePassword\Controller', 'changePersonalPassword');
$this->create('settings_ajax_setlanguage', '/settings/ajax/setlanguage.php')
	->actionInclude('settings/ajax/setlanguage.php');
// apps
$this->create('settings_ajax_enableapp', '/settings/ajax/enableapp.php')
	->actionInclude('settings/ajax/enableapp.php');
$this->create('settings_ajax_disableapp', '/settings/ajax/disableapp.php')
	->actionInclude('settings/ajax/disableapp.php');
$this->create('settings_ajax_updateapp', '/settings/ajax/updateapp.php')
	->actionInclude('settings/ajax/updateapp.php');
$this->create('settings_ajax_uninstallapp', '/settings/ajax/uninstallapp.php')
	->actionInclude('settings/ajax/uninstallapp.php');
$this->create('settings_ajax_navigationdetect', '/settings/ajax/navigationdetect.php')
	->actionInclude('settings/ajax/navigationdetect.php');
// admin
$this->create('settings_ajax_excludegroups', '/settings/ajax/excludegroups.php')
	->actionInclude('settings/ajax/excludegroups.php');
