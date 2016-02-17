<?php

namespace OCA\Activity;

use OCP\Activity\IManager;
use OCP\IL10N;

class GroupHelper
{
	/** @var array */
	protected $activities = array();

	/** @var array */
	protected $openGroup = array();

	/** @var string */
	protected $groupKey = '';

	/** @var int */
	protected $groupTime = 0;

	/** @var bool */
	protected $allowGrouping;

	/** @var \OCP\Activity\IManager */
	protected $activityManager;

	/** @var \OCA\Activity\DataHelper */
	protected $dataHelper;

	/**
	 * @param \OCP\Activity\IManager $activityManager
	 * @param \OCA\Activity\DataHelper $dataHelper
	 * @param bool $allowGrouping
	 */
	public function __construct(IManager $activityManager, DataHelper $dataHelper, $allowGrouping) {
		$this->allowGrouping = $allowGrouping;

		$this->activityManager = $activityManager;
		$this->dataHelper = $dataHelper;
	}

	/**
	 * @param string $user
	 */
	public function setUser($user) {
		$this->dataHelper->setUser($user);
	}

	/**
	 * @param IL10N $l
	 */
	public function setL10n(IL10N $l) {
		$this->dataHelper->setL10n($l);
	}

	/**
	 * Add an activity to the internal array
	 *
	 * @param array $activity
	 */
	public function addActivity($activity) {
		$activity['subjectparams_array'] = $this->dataHelper->getParameters($activity['subjectparams']);
		$activity['messageparams_array'] = $this->dataHelper->getParameters($activity['messageparams']);

		if (!$this->getGroupKey($activity)) {
			if (!empty($this->openGroup)) {
				$this->activities[] = $this->openGroup;
				$this->openGroup = array();
				$this->groupKey = '';
				$this->groupTime = 0;
			}
			$this->activities[] = $activity;
			return;
		}

		// Only group when the event has the same group key
		// and the time difference is not bigger than 3 days.
		if ($this->getGroupKey($activity) === $this->groupKey &&
			abs($activity['timestamp'] - $this->groupTime) < (3 * 24 * 60 * 60)
		) {
			$parameter = $this->getGroupParameter($activity);
			if ($parameter !== false) {
				if (!is_array($this->openGroup['subjectparams_array'][$parameter])) {
					$this->openGroup['subjectparams_array'][$parameter] = array($this->openGroup['subjectparams_array'][$parameter]);
				}
				if (!isset($this->openGroup['activity_ids'])) {
					$this->openGroup['activity_ids'] = array((int) $this->openGroup['activity_id']);
				}

				$this->openGroup['subjectparams_array'][$parameter][] = $activity['subjectparams_array'][$parameter];
				$this->openGroup['subjectparams_array'][$parameter] = array_unique($this->openGroup['subjectparams_array'][$parameter]);
				$this->openGroup['activity_ids'][] = (int) $activity['activity_id'];
			}
		} else {
			if (!empty($this->openGroup)) {
				$this->activities[] = $this->openGroup;
			}

			$this->groupKey = $this->getGroupKey($activity);
			$this->groupTime = $activity['timestamp'];
			$this->openGroup = $activity;
		}
	}

	/**
	 * Get grouping key for an activity
	 *
	 * @param array $activity
	 * @return false|string False, if grouping is not allowed, grouping key otherwise
	 */
	protected function getGroupKey($activity) {
		if ($this->getGroupParameter($activity) === false) {
			return false;
		}

		// FIXME
		// Non-local users are currently not distinguishable, so grouping them might
		// remove the information how many different users performed the same action.
		// So we do not group them anymore, until we found another solution.
		if ($activity['user'] === '') {
			return false;
		}

		return $activity['app'] . '|' . $activity['user'] . '|' . $activity['subject'];
	}

	protected function getGroupParameter($activity) {
		if (!$this->allowGrouping) {
			return false;
		}

		// Allow other apps to group their notifications
		return $this->activityManager->getGroupParameter($activity);
	}

	/**
	 * Get the prepared activities
	 *
	 * @return array translated activities ready for use
	 */
	public function getActivities() {
		if (!empty($this->openGroup)) {
			$this->activities[] = $this->openGroup;
		}

		$return = array();
		foreach ($this->activities as $activity) {
			$activity = $this->dataHelper->formatStrings($activity, 'subject');
			$activity = $this->dataHelper->formatStrings($activity, 'message');

			$activity['typeicon'] = $this->activityManager->getTypeIcon($activity['type']);
			$return[] = $activity;
		}

		return $return;
	}
}
