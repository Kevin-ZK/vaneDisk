<?php
namespace OC\Repair;

use OC\Files\View;
use OC\Hooks\BasicEmitter;

class Preview extends BasicEmitter implements \OC\RepairStep {

	public function getName() {
		return 'Cleaning-up broken previews';
	}

	public function run() {
		$view = new View('/');
		$children = $view->getDirectoryContent('/');

		foreach ($children as $child) {
			if ($view->is_dir($child->getPath())) {
				$thumbnailsFolder = $child->getPath() . '/thumbnails';
				if ($view->is_dir($thumbnailsFolder)) {
					$view->rmdir($thumbnailsFolder);
				}
			}
		}
	}
}
