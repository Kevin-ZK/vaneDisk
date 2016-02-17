<?php

namespace OCA\Activity;

use OCP\Defaults;
use OCP\IDateTimeFormatter;
use OCP\IDBConnection;
use OCP\IURLGenerator;
use OCP\IUser;
use OCP\IUserManager;
use OCP\Mail\IMailer;
use OCP\Template;
use OCP\Util;

/**
 * Class MailQueueHandler
 * Gets the users from the database and
 *
 * @package OCA\Activity
 */
class MailQueueHandler {
	/** Number of entries we want to list in the email */
	const ENTRY_LIMIT = 200;

	/** @var array */
	protected $languages;

	/** @var string */
	protected $senderAddress;

	/** @var string */
	protected $senderName;

	/** @var IDateTimeFormatter */
	protected $dateFormatter;

	/** @var DataHelper */
	protected $dataHelper;

	/** @var IDBConnection */
	protected $connection;

	/** @var IMailer */
	protected $mailer;

	/** @var IURLGenerator */
	protected $urlGenerator;

	/** @var IUserManager */
	protected $userManager;

	/**
	 * Constructor
	 *
	 * @param IDateTimeFormatter $dateFormatter
	 * @param IDBConnection $connection
	 * @param DataHelper $dataHelper
	 * @param IMailer $mailer
	 * @param IURLGenerator $urlGenerator
	 * @param IUserManager $userManager
	 */
	public function __construct(IDateTimeFormatter $dateFormatter,
								IDBConnection $connection,
								DataHelper $dataHelper,
								IMailer $mailer,
								IURLGenerator $urlGenerator,
								IUserManager $userManager) {
		$this->dateFormatter = $dateFormatter;
		$this->connection = $connection;
		$this->dataHelper = $dataHelper;
		$this->mailer = $mailer;
		$this->urlGenerator = $urlGenerator;
		$this->userManager = $userManager;
	}

	/**
	 * Get the users we want to send an email to
	 *
	 * @param int|null $limit
	 * @param int $latestSend
	 * @return array
	 */
	public function getAffectedUsers($limit, $latestSend) {
		$limit = (!$limit) ? null : (int) $limit;

		$query = $this->connection->prepare(
			'SELECT `amq_affecteduser`, MIN(`amq_latest_send`) AS `amq_trigger_time` '
			. ' FROM `*PREFIX*activity_mq` '
			. ' WHERE `amq_latest_send` < ? '
			. ' GROUP BY `amq_affecteduser` '
			. ' ORDER BY `amq_trigger_time` ASC',
			$limit);
		$query->execute(array($latestSend));

		$affectedUsers = array();
		while ($row = $query->fetch()) {
			$affectedUsers[] = $row['amq_affecteduser'];
		}

		return $affectedUsers;
	}

	/**
	 * Get all items for the user we want to send an email to
	 *
	 * @param string $affectedUser
	 * @param int $maxTime
	 * @param int $maxNumItems
	 * @return array [data of the first max. 200 entries, total number of entries]
	 */
	protected function getItemsForUser($affectedUser, $maxTime, $maxNumItems = self::ENTRY_LIMIT) {
		$query = $this->connection->prepare(
			'SELECT * '
			. ' FROM `*PREFIX*activity_mq` '
			. ' WHERE `amq_timestamp` <= ? '
			. ' AND `amq_affecteduser` = ? '
			. ' ORDER BY `amq_timestamp` ASC',
			$maxNumItems
		);
		$query->execute([(int) $maxTime, $affectedUser]);

		$activities = array();
		while ($row = $query->fetch()) {
			$activities[] = $row;
		}

		if (isset($activities[$maxNumItems - 1])) {
			// Reached the limit, run a query to get the actual count.
			$query = $this->connection->prepare(
				'SELECT COUNT(*) AS `actual_count`'
				. ' FROM `*PREFIX*activity_mq` '
				. ' WHERE `amq_timestamp` <= ? '
				. ' AND `amq_affecteduser` = ?'
			);
			$query->execute([(int) $maxTime, $affectedUser]);

			$row = $query->fetch();
			return [$activities, $row['actual_count'] - $maxNumItems];
		} else {
			return [$activities, 0];
		}
	}

	/**
	 * Get a language object for a specific language
	 *
	 * @param string $lang Language identifier
	 * @return \OCP\IL10N Language object of $lang
	 */
	protected function getLanguage($lang) {
		if (!isset($this->languages[$lang])) {
			$this->languages[$lang] = Util::getL10N('activity', $lang);
		}

		return $this->languages[$lang];
	}

	/**
	 * Get the sender data
	 * @param string $setting Either `email` or `name`
	 * @return string
	 */
	protected function getSenderData($setting) {
		if (empty($this->senderAddress)) {
			$this->senderAddress = Util::getDefaultEmailAddress('no-reply');
		}
		if (empty($this->senderName)) {
			$defaults = new Defaults();
			$this->senderName = $defaults->getName();
		}

		if ($setting === 'email') {
			return $this->senderAddress;
		}
		return $this->senderName;
	}

	/**
	 * Get the selected setting for a time frame
	 * However we are not very accurate here, so we match the setting of the user
	 * a bit better.
	 *
	 * @param int $timestamp
	 * @return int Email send option that is used.
	 */
	protected function getLangForApproximatedTimeFrame($timestamp) {
		if (time() - $timestamp < 4000) {
			return UserSettings::EMAIL_SEND_HOURLY;
		} else if (time() - $timestamp < 90000) {
			return UserSettings::EMAIL_SEND_DAILY;
		} else {
			return UserSettings::EMAIL_SEND_WEEKLY;
		}
	}

	/**
	 * Send a notification to one user
	 *
	 * @param string $userName Username of the recipient
	 * @param string $email Email address of the recipient
	 * @param string $lang Selected language of the recipient
	 * @param string $timezone Selected timezone of the recipient
	 * @param int $maxTime
	 */
	public function sendEmailToUser($userName, $email, $lang, $timezone, $maxTime) {
		$user = $this->userManager->get($userName);
		if (!$user instanceof IUser) {
			return;
		}

		list($mailData, $skippedCount) = $this->getItemsForUser($userName, $maxTime);

		$l = $this->getLanguage($lang);
		$this->dataHelper->setUser($userName);
		$this->dataHelper->setL10n($l);

		$activityList = array();
		foreach ($mailData as $activity) {
			$relativeDateTime = $this->dateFormatter->formatDateTimeRelativeDay(
				$activity['amq_timestamp'],
				'long', 'medium',
				new \DateTimeZone($timezone), $l
			);

			$activityList[] = array(
				$this->dataHelper->translation(
					$activity['amq_appid'], $activity['amq_subject'], $this->dataHelper->getParameters($activity['amq_subjectparams'])
				),
				$relativeDateTime,
			);
		}

		$alttext = new Template('activity', 'email.notification', '');
		$alttext->assign('username', $user->getDisplayName());
		$alttext->assign('timeframe', $this->getLangForApproximatedTimeFrame($mailData[0]['amq_timestamp']));
		$alttext->assign('activities', $activityList);
		$alttext->assign('skippedCount', $skippedCount);
		$alttext->assign('owncloud_installation', $this->urlGenerator->getAbsoluteURL('/'));
		$alttext->assign('overwriteL10N', $l);
		$emailText = $alttext->fetchPage();

		$message = $this->mailer->createMessage();
		$message->setTo([$email => $user->getDisplayName()]);
		$message->setSubject((string) $l->t('Activity notification'));
		$message->setPlainBody($emailText);
		$message->setFrom([$this->getSenderData('email') => $this->getSenderData('name')]);
		$this->mailer->send($message);
	}

	/**
	 * Delete all entries we dealt with
	 *
	 * @param array $affectedUsers
	 * @param int $maxTime
	 */
	public function deleteSentItems($affectedUsers, $maxTime) {
		$placeholders = implode(',', array_fill(0, sizeof($affectedUsers), '?'));
		$queryParams = $affectedUsers;
		array_unshift($queryParams, (int) $maxTime);

		$query = $this->connection->prepare(
			'DELETE FROM `*PREFIX*activity_mq` '
			. ' WHERE `amq_timestamp` <= ? '
			. ' AND `amq_affecteduser` IN (' . $placeholders . ')');
		$query->execute($queryParams);
	}
}
