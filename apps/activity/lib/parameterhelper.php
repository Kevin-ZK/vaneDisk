<?php

/**
 * ownCloud - Activity App
 *
 * @copyright 2014 Joas Schilling nickvergessen@owncloud.com
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU AFFERO GENERAL PUBLIC LICENSE
 * License as published by the Free Software Foundation; either
 * version 3 of the License, or any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU AFFERO GENERAL PUBLIC LICENSE for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library.  If not, see <http://www.gnu.org/licenses/>.
 *
 */

namespace OCA\Activity;

use OCP\Activity\IManager;
use OCP\IConfig;
use OCP\IL10N;
use OCP\IUserManager;
use OCP\User;
use OCP\Util;
use OC\Files\View;

class ParameterHelper {
	/** @var \OCP\Activity\IManager */
	protected $activityManager;

	/** @var \OCP\IUserManager */
	protected $userManager;

	/** @var \OC\Files\View */
	protected $rootView;

	/** @var \OCP\IL10N */
	protected $l;

	/** @var \OCP\IConfig */
	protected $config;

	/** @var string */
	protected $user;

	/**
	 * @param IManager $activityManager
	 * @param IUserManager $userManager
	 * @param View $rootView
	 * @param IConfig $config
	 * @param IL10N $l
	 * @param string $user
	 */
	public function __construct(IManager $activityManager, IUserManager $userManager, View $rootView, IConfig $config, IL10N $l, $user) {
		$this->activityManager = $activityManager;
		$this->userManager = $userManager;
		$this->rootView = $rootView;
		$this->config = $config;
		$this->l = $l;
		$this->user = $user;
	}

	/**
	 * @param string $user
	 */
	public function setUser($user) {
		$this->user = (string) $user;
	}

	/**
	 * @param IL10N $l
	 */
	public function setL10n(IL10N $l) {
		$this->l = $l;
	}

	/**
	 * Prepares the parameters before we use them in the subject or message
	 * @param array $params
	 * @param array $paramTypes Type of parameters, if they need special handling
	 * @param bool $stripPath Shall we remove the path from the filename
	 * @param bool $highlightParams
	 * @return array
	 */
	public function prepareParameters($params, $paramTypes = array(), $stripPath = false, $highlightParams = false) {
		$preparedParams = array();
		foreach ($params as $i => $param) {
			if (is_array($param)) {
				$preparedParams[] = $this->prepareArrayParameter($param, isset($paramTypes[$i]) ? $paramTypes[$i] : '', $stripPath, $highlightParams);
			} else {
				$preparedParams[] = $this->prepareStringParameter($param, isset($paramTypes[$i]) ? $paramTypes[$i] : '', $stripPath, $highlightParams);
			}
		}
		return $preparedParams;
	}

	/**
	 * Prepares a string parameter before we use it in the subject or message
	 *
	 * @param string $param
	 * @param string $paramType Type of parameter, if it needs special handling
	 * @param bool $stripPath Shall we remove the path from the filename
	 * @param bool $highlightParams
	 * @return string
	 */
	public function prepareStringParameter($param, $paramType, $stripPath, $highlightParams) {
		if ($paramType === 'file') {
			return $this->prepareFileParam($param, $stripPath, $highlightParams);
		} else if ($paramType === 'username') {
			return $this->prepareUserParam($param, $highlightParams);
		}
		return $this->prepareParam($param, $highlightParams);
	}

	/**
	 * Prepares an array parameter before we use it in the subject or message
	 *
	 * @param array $params
	 * @param string $paramType Type of parameters, if it needs special handling
	 * @param bool $stripPath Shall we remove the path from the filename
	 * @param bool $highlightParams
	 * @return string
	 */
	public function prepareArrayParameter($params, $paramType, $stripPath, $highlightParams) {
		$parameterList = $plainParameterList = array();
		foreach ($params as $parameter) {
			$parameterList[] = $this->prepareStringParameter($parameter, $paramType, $stripPath, $highlightParams);
			$plainParameterList[] = $this->prepareStringParameter($parameter, $paramType, false, false);
		}
		return $this->joinParameterList($parameterList, $plainParameterList, $highlightParams);
	}

	/**
	 * Prepares a parameter for usage by adding highlights
	 *
	 * @param string $param
	 * @param bool $highlightParams
	 * @return string
	 */
	protected function prepareParam($param, $highlightParams) {
		if ($highlightParams) {
			return '<strong>' . Util::sanitizeHTML($param) . '</strong>';
		} else {
			return $param;
		}
	}

	/**
	 * Prepares a user name parameter for usage
	 *
	 * Add an avatar to usernames
	 *
	 * @param string $param
	 * @param bool $highlightParams
	 * @return string
	 */
	protected function prepareUserParam($param, $highlightParams) {
		// If the username is empty, the action has been performed by a remote
		// user, or via a public share. We don't know the username in that case
		if ($param === '') {
			if ($highlightParams) {
				return '<strong>' . $this->l->t('"remote user"') . '</strong>';
			} else {
				return $this->l->t('"remote user"');
			}
		}

		$user = $this->userManager->get($param);
		$displayName = ($user) ? $user->getDisplayName() : $param;
		$param = Util::sanitizeHTML($param);
		$displayName = Util::sanitizeHTML($displayName);

		if ($highlightParams) {
			$avatarPlaceholder = '';
			if ($this->config->getSystemValue('enable_avatars', true)) {
				$avatarPlaceholder = '<div class="avatar" data-user="' . $param . '"></div>';
			}
			return $avatarPlaceholder . '<strong>' . $displayName . '</strong>';
		} else {
			return $displayName;
		}
	}

	/**
	 * Prepares a file parameter for usage
	 *
	 * Removes the path from filenames and adds highlights
	 *
	 * @param string $param
	 * @param bool $stripPath Shall we remove the path from the filename
	 * @param bool $highlightParams
	 * @return string
	 */
	protected function prepareFileParam($param, $stripPath, $highlightParams) {
		$param = $this->fixLegacyFilename($param);
		$is_dir = $this->rootView->is_dir('/' . $this->user . '/files' . $param);

		if ($is_dir) {
			$fileLink = Util::linkTo('files', 'index.php', array('dir' => $param));
		} else {
			$parentDir = (substr_count($param, '/') === 1) ? '/' : dirname($param);
			$fileName = basename($param);
			$fileLink = Util::linkTo('files', 'index.php', array(
				'dir' => $parentDir,
				'scrollto' => $fileName,
			));
		}

		$param = trim($param, '/');
		list($path, $name) = $this->splitPathFromFilename($param);
		if (!$stripPath || $path === '') {
			if (!$highlightParams) {
				return $param;
			}
			return '<a class="filename" href="' . $fileLink . '">' . Util::sanitizeHTML($param) . '</a>';
		}

		if (!$highlightParams) {
			return $name;
		}

		$title = ' title="' . $this->l->t('in %s', array(Util::sanitizeHTML($path))) . '"';
		return '<a class="filename tooltip" href="' . $fileLink . '"' . $title . '>' . Util::sanitizeHTML($name) . '</a>';
	}

	/**
	 * Prepend leading slash to filenames of legacy activities
	 * @param string $filename
	 * @return string
	 */
	protected function fixLegacyFilename($filename) {
		if (strpos($filename, '/') !== 0) {
			return '/' . $filename;
		}
		return $filename;
	}

	/**
	 * Split the path from the filename string
	 *
	 * @param string $filename
	 * @return array Array with path and filename
	 */
	protected function splitPathFromFilename($filename) {
		if (strrpos($filename, '/') !== false) {
			return array(
				trim(substr($filename, 0, strrpos($filename, '/')), '/'),
				substr($filename, strrpos($filename, '/') + 1),
			);
		}
		return array('', $filename);
	}

	/**
	 * Returns a list of grouped parameters
	 *
	 * 2 parameters are joined by "and":
	 * => A and B
	 * Up to 5 parameters are joined by "," and "and":
	 * => A, B, C, D and E
	 * More than 5 parameters are joined by "," and trimmed:
	 * => A, B, C and #n more
	 *
	 * @param array $parameterList
	 * @param array $plainParameterList
	 * @param bool $highlightParams
	 * @return string
	 */
	protected function joinParameterList($parameterList, $plainParameterList, $highlightParams) {
		if (empty($parameterList)) {
			return '';
		}

		$count = sizeof($parameterList);
		$lastItem = array_pop($parameterList);

		if ($count === 1)
		{
			return $lastItem;
		}
		else if ($count === 2)
		{
			$firstItem = array_pop($parameterList);
			return $this->l->t('%s and %s', array($firstItem, $lastItem));
		}
		else if ($count <= 5)
		{
			$list = implode($this->l->t(', '), $parameterList);
			return $this->l->t('%s and %s', array($list, $lastItem));
		}

		$firstParams = array_slice($parameterList, 0, 3);
		$firstList = implode($this->l->t(', '), $firstParams);
		$trimmedParams = array_slice($plainParameterList, 3);
		$trimmedList = implode($this->l->t(', '), $trimmedParams);
		if ($highlightParams) {
			return $this->l->n(
				'%s and <strong class="tooltip" title="%s">%n more</strong>',
				'%s and <strong class="tooltip" title="%s">%n more</strong>',
				$count - 3,
				array($firstList, Util::sanitizeHTML($trimmedList)));
		}
		return $this->l->n('%s and %n more', '%s and %n more', $count - 3, array($firstList));
	}

	/**
	 * List with special parameters for the message
	 *
	 * @param string $app
	 * @param string $text
	 * @return array
	 */
	public function getSpecialParameterList($app, $text) {
		$specialParameters = $this->activityManager->getSpecialParameterList($app, $text);

		if ($specialParameters !== false) {
			return $specialParameters;
		}

		return array();
	}
}
