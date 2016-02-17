<?php

namespace OCA\Activity;

/**
 * Class Api
 *
 * @package OCA\Activity
 */
class Api
{
	const DEFAULT_LIMIT = 30;

	static public function get($param) {
		$app = new AppInfo\Application();
		$data = $app->getContainer()->query('ActivityData');

		$start = isset($_GET['start']) ? $_GET['start'] : 0;
		$count = isset($_GET['count']) ? $_GET['count'] : self::DEFAULT_LIMIT;

		$activities = $data->read(
			$app->getContainer()->query('GroupHelper'),
			$app->getContainer()->query('UserSettings'),
			$start, $count, 'all'
		);

		$entries = array();
		foreach($activities as $entry) {
			$entries[] = array(
				'id' => $entry['activity_id'],
				'subject' => (string) $entry['subjectformatted']['full'],
				'message' => (string) $entry['messageformatted']['full'],
				'file' => $entry['file'],
				'link' => $entry['link'],
				'date' => date('c', $entry['timestamp']),
			);
		}

		return new \OC_OCS_Result($entries);
	}
}
