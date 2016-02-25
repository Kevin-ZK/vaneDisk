<?php

use OCA\External\External;

OCP\App::registerAdmin('external', 'settings');

$sites = External::getSites();
if (!empty($sites)) {
	$urlGenerator = \OC::$server->getURLGenerator();
	$navigationManager = \OC::$server->getNavigationManager();
	for ($i = 0; $i < sizeof($sites); $i++) {
		$navigationEntry = function () use ($i, $urlGenerator, $sites) {
			return [
				'id'    => 'external_index' . ($i + 1),
				'order' => 80 + $i,
				'href' => $urlGenerator->linkToRoute('external_index', ['id'=> $i + 1]),
				'icon' => $urlGenerator->imagePath('external', !empty($sites[$i][2]) ? $sites[$i][2] : 'external.svg'),
				'name' => $sites[$i][0],
			];
		};
		$navigationManager->add($navigationEntry);
	}
}
