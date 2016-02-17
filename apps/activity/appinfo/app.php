<?php

namespace OCA\Activity\AppInfo;

$app = new Application();
$c = $app->getContainer();

// add an navigation entry
$navigationEntry = function () use ($c) {
	return [
		'id' => $c->getAppName(),
		'order' => 1,
		'name' => $c->query('ActivityL10N')->t('Activity'),
		'href' => $c->query('URLGenerator')->linkToRoute('activity.Activities.showList'),
		'icon' => $c->query('URLGenerator')->imagePath('activity', 'activity.svg'),
	];
};
$c->getServer()->getNavigationManager()->add($navigationEntry);

// register the hooks for filesystem operations. All other events from other apps has to be send via the public api
\OCA\Activity\FilesHooksStatic::register();
\OCP\Util::connectHook('OC_User', 'post_deleteUser', 'OCA\Activity\Hooks', 'deleteUser');
\OCA\Activity\Consumer::register($c->getServer()->getActivityManager(), $c);

// Personal settings for notifications and emails
\OCP\App::registerPersonal($c->getAppName(), 'personal');

// Cron job for sending emails and pruning the activity list
$c->getServer()->getJobList()->add('OCA\Activity\BackgroundJob\EmailNotification');
$c->getServer()->getJobList()->add('OCA\Activity\BackgroundJob\ExpireActivities');
