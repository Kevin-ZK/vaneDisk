<?php

namespace OCA\Files;

use OCP\Files\Folder;
use OCP\ITagManager;

class ActivityHelper {
	/** If a user has a lot of favorites the query might get too slow and long */
	const FAVORITE_LIMIT = 50;

	/** @var \OCP\ITagManager */
	protected $tagManager;

	/**
	 * @param ITagManager $tagManager
	 */
	public function __construct(ITagManager $tagManager) {
		$this->tagManager = $tagManager;
	}

	/**
	 * Returns an array with the favorites
	 *
	 * @param string $user
	 * @return array
	 * @throws \RuntimeException when too many or no favorites where found
	 */
	public function getFavoriteFilePaths($user) {
		$tags = $this->tagManager->load('files', [], false, $user);
		$favorites = $tags->getFavorites();

		if (empty($favorites)) {
			throw new \RuntimeException('No favorites', 1);
		} else if (isset($favorites[self::FAVORITE_LIMIT])) {
			throw new \RuntimeException('Too many favorites', 2);
		}

		// Can not DI because the user is not known on instantiation
		$rootFolder = \OC::$server->getUserFolder($user);
		$folders = $items = [];
		foreach ($favorites as $favorite) {
			$nodes = $rootFolder->getById($favorite);
			if (!empty($nodes)) {
				/** @var \OCP\Files\Node $node */
				$node = array_shift($nodes);
				$path = substr($node->getPath(), strlen($user . '/files/'));

				$items[] = $path;
				if ($node instanceof Folder) {
					$folders[] = $path;
				}
			}
		}

		if (empty($items)) {
			throw new \RuntimeException('No favorites', 1);
		}

		return [
			'items' => $items,
			'folders' => $folders,
		];
	}
}
