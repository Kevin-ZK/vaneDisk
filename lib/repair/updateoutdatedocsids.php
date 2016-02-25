<?php

namespace OC\Repair;

use OC\Hooks\BasicEmitter;
use OC\RepairStep;
use OCP\IConfig;

/**
 * Class UpdateOutdatedOcsIds is used to update invalid outdated OCS IDs, this is
 * for example the case when an application has had another OCS ID in the past such
 * as for contacts and calendar when apps.owncloud.com migrated to a unified identifier
 * for multiple versions.
 *
 * @package OC\Repair
 */
class UpdateOutdatedOcsIds extends BasicEmitter implements RepairStep {
	/** @var IConfig */
	private $config;

	/**
	 * @param IConfig $config
	 */
	public function __construct(IConfig $config) {
		$this->config = $config;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getName() {
		return 'Repair outdated OCS IDs';
	}

	/**
	 * @param string $appName
	 * @param string $oldId
	 * @param string $newId
	 * @return bool True if updated, false otherwise
	 */
	public function fixOcsId($appName, $oldId, $newId) {
		$existingId = $this->config->getAppValue($appName, 'ocsid');

		if($existingId === $oldId) {
			$this->config->setAppValue($appName, 'ocsid', $newId);
			return true;
		}

		return false;
	}

	/**
	 * {@inheritdoc}
	 */
	public function run() {
		$appsToUpdate = [
			'contacts' => [
				'old' => '166044',
				'new' => '168708',
			],
			'calendar' => [
				'old' => '166043',
				'new' => '168707',
			],
			'bookmarks' => [
				'old' => '166042',
				'new' => '168710',
			],
			'search_lucene' => [
				'old' => '166057',
				'new' => '168709',
			],
			'documents' => [
				'old' => '166045',
				'new' => '168711',
			]
		];

		foreach($appsToUpdate as $appName => $ids) {
			if ($this->fixOcsId($appName, $ids['old'], $ids['new'])) {
				$this->emit(
					'\OC\Repair',
					'info',
					[sprintf('Fixed invalid %s OCS id', $appName)]
				);
			}
		}
	}
}
