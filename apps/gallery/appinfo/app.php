<?php

\OC::$server->getNavigationManager()->add(function () {
	$urlGenerator = \OC::$server->getURLGenerator();
	$l = \OC::$server->getL10N('gallery');
	return [
		'id' => 'gallery_index',
		'order' => 3,
		'href' => $urlGenerator->linkToRoute('gallery_index'),
		'icon' => $urlGenerator->imagePath('gallery', 'gallery.svg'),
		'name' => $l->t('Pictures'),
	];
});

// make slideshow available in files and public shares
OCP\Util::addScript('gallery', 'jquery.mousewheel-3.1.1');
OCP\Util::addScript('gallery', 'slideshow');
OCP\Util::addScript('gallery', 'public');
OCP\Util::addStyle('gallery', 'slideshow');

