<?php

OCP\User::checkLoggedIn();
OCP\App::checkAppEnabled('gallery');
OCP\App::setActiveNavigationEntry('gallery_index');

OCP\Util::addStyle('gallery', 'styles');
OCP\Util::addStyle('gallery', 'mobile');

OCP\Util::addScript('gallery', 'album');
OCP\Util::addScript('gallery', 'gallery');
OCP\Util::addScript('gallery', 'thumbnail');

$tmpl = new OCP\Template('gallery', 'index', 'user');
$tmpl->printPage();
