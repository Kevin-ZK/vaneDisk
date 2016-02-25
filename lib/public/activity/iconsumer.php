<?php

/**
 * Public interface of vanedisk for apps to use.
 * Activity/IConsumer interface
 */

// use OCP namespace for all classes that are considered public.
// This means that they should be used by apps instead of the internal ownCloud classes
namespace OCP\Activity;

/**
 * Interface IConsumer
 *
 * @package OCP\Activity
 * @since 6.0.0
 */
interface IConsumer {
	/**
	 * @param $app
	 * @param $subject
	 * @param $subjectParams
	 * @param $message
	 * @param $messageParams
	 * @param $file
	 * @param $link
	 * @param $affectedUser
	 * @param $type
	 * @param $priority
	 * @return mixed
	 * @since 6.0.0
	 */
	function receive($app, $subject, $subjectParams, $message, $messageParams, $file, $link, $affectedUser, $type, $priority );
}

