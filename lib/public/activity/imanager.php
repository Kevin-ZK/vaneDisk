<?php

// use OCP namespace for all classes that are considered public.
// This means that they should be used by apps instead of the internal vanedisk classes
namespace OCP\Activity;

/**
 * Interface IManager
 *
 * @package OCP\Activity
 * @since 6.0.0
 */
interface IManager {

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
	function publishActivity($app, $subject, $subjectParams, $message, $messageParams, $file, $link, $affectedUser, $type, $priority);

	/**
	 * In order to improve lazy loading a closure can be registered which will be called in case
	 * activity consumers are actually requested
	 *
	 * $callable has to return an instance of \OCP\Activity\IConsumer
	 *
	 * @param \Closure $callable
	 * @return void
	 * @since 6.0.0
	 */
	function registerConsumer(\Closure $callable);

	/**
	 * In order to improve lazy loading a closure can be registered which will be called in case
	 * activity consumers are actually requested
	 *
	 * $callable has to return an instance of \OCP\Activity\IExtension
	 *
	 * @param \Closure $callable
	 * @return void
	 * @since 8.0.0
	 */
	function registerExtension(\Closure $callable);

	/**
	 * Will return additional notification types as specified by other apps
	 * @param string $languageCode
	 * @return array
	 * @since 8.0.0
	 */
	function getNotificationTypes($languageCode);

	/**
	 * @param string $method
	 * @return array
	 * @since 8.0.0
	 */
	function getDefaultTypes($method);

	/**
	 * @param string $type
	 * @return string
	 * @since 8.0.0
	 */
	function getTypeIcon($type);

	/**
	 * @param string $app
	 * @param string $text
	 * @param array $params
	 * @param boolean $stripPath
	 * @param boolean $highlightParams
	 * @param string $languageCode
	 * @return string|false
	 * @since 8.0.0
	 */
	function translate($app, $text, $params, $stripPath, $highlightParams, $languageCode);

	/**
	 * @param string $app
	 * @param string $text
	 * @return array|false
	 * @since 8.0.0
	 */
	function getSpecialParameterList($app, $text);

	/**
	 * @param array $activity
	 * @return integer|false
	 * @since 8.0.0
	 */
	function getGroupParameter($activity);

	/**
	 * @return array
	 * @since 8.0.0
	 */
	function getNavigation();

	/**
	 * @param string $filterValue
	 * @return boolean
	 * @since 8.0.0
	 */
	function isFilterValid($filterValue);

	/**
	 * @param array $types
	 * @param string $filter
	 * @return array
	 * @since 8.0.0
	 */
	function filterNotificationTypes($types, $filter);

	/**
	 * @param string $filter
	 * @return array
	 * @since 8.0.0
	 */
	function getQueryForFilter($filter);

	/**
	 * Get the user we need to use
	 *
	 * Either the user is logged in, or we try to get it from the token
	 *
	 * @return string
	 * @throws \UnexpectedValueException If the token is invalid, does not exist or is not unique
	 * @since 8.1.0
	 */
	public function getCurrentUserId();
}
