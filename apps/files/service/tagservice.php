<?php

namespace OCA\Files\Service;

use OC\Files\FileInfo;

/**
 * Service class to manage tags on files.
 */
class TagService {

	/**
	 * @var \OCP\IUserSession
	 */
	private $userSession;

	/**
	 * @var \OCP\ITags
	 */
	private $tagger;

	/**
	 * @var \OCP\Files\Folder
	 */
	private $homeFolder;

	public function __construct(
		\OCP\IUserSession $userSession,
		\OCP\ITags $tagger,
		\OCP\Files\Folder $homeFolder
	) {
		$this->userSession = $userSession;
		$this->tagger = $tagger;
		$this->homeFolder = $homeFolder;
	}

	/**
	 * Updates the tags of the specified file path.
	 * The passed tags are absolute, which means they will
	 * replace the actual tag selection.
	 *
	 * @param string $path path
	 * @param array  $tags array of tags
	 * @return array list of tags
	 * @throws \OCP\Files\NotFoundException if the file does not exist
	 */
	public function updateFileTags($path, $tags) {
		$fileId = $this->homeFolder->get($path)->getId();

		$currentTags = $this->tagger->getTagsForObjects(array($fileId));

		if (!empty($currentTags)) {
			$currentTags = current($currentTags);
		}

		$newTags = array_diff($tags, $currentTags);
		foreach ($newTags as $tag) {
			$this->tagger->tagAs($fileId, $tag);
		}
		$deletedTags = array_diff($currentTags, $tags);
		foreach ($deletedTags as $tag) {
			$this->tagger->unTag($fileId, $tag);
		}

		// TODO: re-read from tagger to make sure the
		// list is up to date, in case of concurrent changes ?
		return $tags;
	}

	/**
	 * Get all files for the given tag
	 *
	 * @param array $tagName tag name to filter by
	 * @return FileInfo[] list of matching files
	 * @throws \Exception if the tag does not exist
	 */
	public function getFilesByTag($tagName) {
		try {
			$fileIds = $this->tagger->getIdsForTag($tagName);
		} catch (\Exception $e) {
			return [];
		}

		$fileInfos = [];
		foreach ($fileIds as $fileId) {
			$nodes = $this->homeFolder->getById((int) $fileId);
			foreach ($nodes as $node) {
				/** @var \OC\Files\Node\Node $node */
				$fileInfos[] = $node->getFileInfo();
			}
		}
		return $fileInfos;
	}
}

