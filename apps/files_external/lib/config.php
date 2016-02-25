<?php

/**
 * Class to configure mount.json globally and for users
 */
class OC_Mount_Config {
	// TODO: make this class non-static and give it a proper namespace

	const MOUNT_TYPE_GLOBAL = 'global';
	const MOUNT_TYPE_GROUP = 'group';
	const MOUNT_TYPE_USER = 'user';
	const MOUNT_TYPE_PERSONAL = 'personal';

	// getBackendStatus return types
	const STATUS_SUCCESS = 0;
	const STATUS_ERROR = 1;

	// whether to skip backend test (for unit tests, as this static class is not mockable)
	public static $skipTest = false;

	private static $backends = array();

	/**
	 * @param string $class
	 * @param array $definition
	 * @return bool
	 */
	public static function registerBackend($class, $definition) {
		if (!isset($definition['backend'])) {
			return false;
		}

		OC_Mount_Config::$backends[$class] = $definition;
		return true;
	}

	/**
	 * Setup backends
	 *
	 * @return array of previously registered backends
	 */
	public static function setUp($backends = array()) {
		$backup = self::$backends;
		self::$backends = $backends;

		return $backup;
	}

	/**
	 * Get details on each of the external storage backends, used for the mount config UI
	 * If a custom UI is needed, add the key 'custom' and a javascript file with that name will be loaded
	 * If the configuration parameter should be secret, add a '*' to the beginning of the value
	 * If the configuration parameter is a boolean, add a '!' to the beginning of the value
	 * If the configuration parameter is optional, add a '&' to the beginning of the value
	 * If the configuration parameter is hidden, add a '#' to the beginning of the value
	 *
	 * @return array
	 */
	public static function getBackends() {
		$sortFunc = function ($a, $b) {
			return strcasecmp($a['backend'], $b['backend']);
		};

		$backEnds = array();

		foreach (OC_Mount_Config::$backends as $class => $backend) {
			if (isset($backend['has_dependencies']) and $backend['has_dependencies'] === true) {
				if (!method_exists($class, 'checkDependencies')) {
					\OCP\Util::writeLog('files_external',
						"Backend class $class has dependencies but doesn't provide method checkDependencies()",
						\OCP\Util::DEBUG);
					continue;
				} elseif ($class::checkDependencies() !== true) {
					continue;
				}
			}
			$backEnds[$class] = $backend;
		}

		uasort($backEnds, $sortFunc);

		return $backEnds;
	}

	/**
	 * Hook that mounts the given user's visible mount points
	 *
	 * @param array $data
	 */
	public static function initMountPointsHook($data) {
		self::addStorageIdToConfig(null);
		if ($data['user']) {
			self::addStorageIdToConfig($data['user']);
			$user = \OC::$server->getUserManager()->get($data['user']);
			if (!$user) {
				\OC_Log::write(
					'files_external',
					'Cannot init external mount points for non-existant user "' . $data['user'] . '".',
					\OC_Log::WARN
				);
				return;
			}
			$userView = new \OC\Files\View('/' . $user->getUID() . '/files');
			$changePropagator = new \OC\Files\Cache\ChangePropagator($userView);
			$etagPropagator = new \OCA\Files_External\EtagPropagator($user, $changePropagator, \OC::$server->getConfig());
			$etagPropagator->propagateDirtyMountPoints();
			\OCP\Util::connectHook(
				\OC\Files\Filesystem::CLASSNAME,
				\OC\Files\Filesystem::signal_create_mount,
				$etagPropagator, 'updateHook');
			\OCP\Util::connectHook(
				\OC\Files\Filesystem::CLASSNAME,
				\OC\Files\Filesystem::signal_delete_mount,
				$etagPropagator, 'updateHook');
		}
	}

	/**
	 * Returns the mount points for the given user.
	 * The mount point is relative to the data directory.
	 *
	 * @param string $user user
	 * @return array of mount point string as key, mountpoint config as value
	 */
	public static function getAbsoluteMountPoints($user) {
		$mountPoints = array();

		$backends = self::getBackends();

		// Load system mount points
		$mountConfig = self::readData();

		// Global mount points (is this redundant?)
		if (isset($mountConfig[self::MOUNT_TYPE_GLOBAL])) {
			foreach ($mountConfig[self::MOUNT_TYPE_GLOBAL] as $mountPoint => $options) {
				$options['personal'] = false;
				$options['options'] = self::decryptPasswords($options['options']);
				if (!isset($options['priority'])) {
					$options['priority'] = $backends[$options['class']]['priority'];
				}

				// Override if priority greater
				if ((!isset($mountPoints[$mountPoint]))
					|| ($options['priority'] >= $mountPoints[$mountPoint]['priority'])
				) {
					$options['priority_type'] = self::MOUNT_TYPE_GLOBAL;
					$options['backend'] = $backends[$options['class']]['backend'];
					$mountPoints[$mountPoint] = $options;
				}
			}
		}
		// All user mount points
		if (isset($mountConfig[self::MOUNT_TYPE_USER]) && isset($mountConfig[self::MOUNT_TYPE_USER]['all'])) {
			$mounts = $mountConfig[self::MOUNT_TYPE_USER]['all'];
			foreach ($mounts as $mountPoint => $options) {
				$mountPoint = self::setUserVars($user, $mountPoint);
				foreach ($options as &$option) {
					$option = self::setUserVars($user, $option);
				}
				$options['personal'] = false;
				$options['options'] = self::decryptPasswords($options['options']);
				if (!isset($options['priority'])) {
					$options['priority'] = $backends[$options['class']]['priority'];
				}

				// Override if priority greater
				if ((!isset($mountPoints[$mountPoint]))
					|| ($options['priority'] >= $mountPoints[$mountPoint]['priority'])
				) {
					$options['priority_type'] = self::MOUNT_TYPE_GLOBAL;
					$options['backend'] = $backends[$options['class']]['backend'];
					$mountPoints[$mountPoint] = $options;
				}
			}
		}
		// Group mount points
		if (isset($mountConfig[self::MOUNT_TYPE_GROUP])) {
			foreach ($mountConfig[self::MOUNT_TYPE_GROUP] as $group => $mounts) {
				if (\OC_Group::inGroup($user, $group)) {
					foreach ($mounts as $mountPoint => $options) {
						$mountPoint = self::setUserVars($user, $mountPoint);
						foreach ($options as &$option) {
							$option = self::setUserVars($user, $option);
						}
						$options['personal'] = false;
						$options['options'] = self::decryptPasswords($options['options']);
						if (!isset($options['priority'])) {
							$options['priority'] = $backends[$options['class']]['priority'];
						}

						// Override if priority greater or if priority type different
						if ((!isset($mountPoints[$mountPoint]))
							|| ($options['priority'] >= $mountPoints[$mountPoint]['priority'])
							|| ($mountPoints[$mountPoint]['priority_type'] !== self::MOUNT_TYPE_GROUP)
						) {
							$options['priority_type'] = self::MOUNT_TYPE_GROUP;
							$options['backend'] = $backends[$options['class']]['backend'];
							$mountPoints[$mountPoint] = $options;
						}
					}
				}
			}
		}
		// User mount points
		if (isset($mountConfig[self::MOUNT_TYPE_USER])) {
			foreach ($mountConfig[self::MOUNT_TYPE_USER] as $mountUser => $mounts) {
				if (strtolower($mountUser) === strtolower($user)) {
					foreach ($mounts as $mountPoint => $options) {
						$mountPoint = self::setUserVars($user, $mountPoint);
						foreach ($options as &$option) {
							$option = self::setUserVars($user, $option);
						}
						$options['personal'] = false;
						$options['options'] = self::decryptPasswords($options['options']);
						if (!isset($options['priority'])) {
							$options['priority'] = $backends[$options['class']]['priority'];
						}

						// Override if priority greater or if priority type different
						if ((!isset($mountPoints[$mountPoint]))
							|| ($options['priority'] >= $mountPoints[$mountPoint]['priority'])
							|| ($mountPoints[$mountPoint]['priority_type'] !== self::MOUNT_TYPE_USER)
						) {
							$options['priority_type'] = self::MOUNT_TYPE_USER;
							$options['backend'] = $backends[$options['class']]['backend'];
							$mountPoints[$mountPoint] = $options;
						}
					}
				}
			}
		}

		$personalBackends = self::getPersonalBackends();

		// Load personal mount points
		$mountConfig = self::readData($user);
		if (isset($mountConfig[self::MOUNT_TYPE_USER][$user])) {
			foreach ($mountConfig[self::MOUNT_TYPE_USER][$user] as $mountPoint => $options) {
				if (isset($personalBackends[$options['class']])) {
					$options['personal'] = true;
					$options['options'] = self::decryptPasswords($options['options']);

					// Always override previous config
					$options['priority_type'] = self::MOUNT_TYPE_PERSONAL;
					$options['backend'] = $backends[$options['class']]['backend'];
					$mountPoints[$mountPoint] = $options;
				}
			}
		}

		return $mountPoints;
	}

	/**
	 * fill in the correct values for $user
	 *
	 * @param string $user user value
	 * @param string|array $input
	 * @return string
	 */
	private static function setUserVars($user, $input) {
		if (is_array($input)) {
			foreach ($input as &$value) {
				if (is_string($value)) {
					$value = str_replace('$user', $user, $value);
				}
			}
		} else {
			if (is_string($input)) {
				$input = str_replace('$user', $user, $input);
			}
		}
		return $input;
	}


	/**
	 * Get details on each of the external storage backends, used for the mount config UI
	 * Some backends are not available as a personal backend, f.e. Local and such that have
	 * been disabled by the admin.
	 *
	 * If a custom UI is needed, add the key 'custom' and a javascript file with that name will be loaded
	 * If the configuration parameter should be secret, add a '*' to the beginning of the value
	 * If the configuration parameter is a boolean, add a '!' to the beginning of the value
	 * If the configuration parameter is optional, add a '&' to the beginning of the value
	 * If the configuration parameter is hidden, add a '#' to the beginning of the value
	 *
	 * @return array
	 */
	public static function getPersonalBackends() {

		// Check whether the user has permissions to add personal storage backends
		// return an empty array if this is not the case
		if (OCP\Config::getAppValue('files_external', 'allow_user_mounting', 'yes') !== 'yes') {
			return array();
		}

		$backEnds = self::getBackends();

		// Remove local storage and other disabled storages
		unset($backEnds['\OC\Files\Storage\Local']);

		$allowedBackEnds = explode(',', OCP\Config::getAppValue('files_external', 'user_mounting_backends', ''));
		foreach ($backEnds as $backend => $null) {
			if (!in_array($backend, $allowedBackEnds)) {
				unset($backEnds[$backend]);
			}
		}

		return $backEnds;
	}

	/**
	 * Get the system mount points
	 * The returned array is not in the same format as getUserMountPoints()
	 *
	 * @return array
	 */
	public static function getSystemMountPoints() {
		$mountPoints = self::readData();
		$backends = self::getBackends();
		$system = array();
		if (isset($mountPoints[self::MOUNT_TYPE_GROUP])) {
			foreach ($mountPoints[self::MOUNT_TYPE_GROUP] as $group => $mounts) {
				foreach ($mounts as $mountPoint => $mount) {
					// Update old classes to new namespace
					if (strpos($mount['class'], 'OC_Filestorage_') !== false) {
						$mount['class'] = '\OC\Files\Storage\\' . substr($mount['class'], 15);
					}
					$mount['options'] = self::decryptPasswords($mount['options']);
					if (!isset($mount['priority'])) {
						$mount['priority'] = $backends[$mount['class']]['priority'];
					}
					// Remove '/$user/files/' from mount point
					$mountPoint = substr($mountPoint, 13);

					$config = array(
						'class' => $mount['class'],
						'mountpoint' => $mountPoint,
						'backend' => $backends[$mount['class']]['backend'],
						'priority' => $mount['priority'],
						'options' => $mount['options'],
						'applicable' => array('groups' => array($group), 'users' => array())
					);
					if (isset($mount['id'])) {
						$config['id'] = (int)$mount['id'];
					}
					if (isset($mount['storage_id'])) {
						$config['storage_id'] = (int)$mount['storage_id'];
					}
					if (isset($mount['mountOptions'])) {
						$config['mountOptions'] = $mount['mountOptions'];
					}
					$hash = self::makeConfigHash($config);
					// If an existing config exists (with same class, mountpoint and options)
					if (isset($system[$hash])) {
						// add the groups into that config
						$system[$hash]['applicable']['groups']
							= array_merge($system[$hash]['applicable']['groups'], array($group));
					} else {
						$system[$hash] = $config;
					}
				}
			}
		}
		if (isset($mountPoints[self::MOUNT_TYPE_USER])) {
			foreach ($mountPoints[self::MOUNT_TYPE_USER] as $user => $mounts) {
				foreach ($mounts as $mountPoint => $mount) {
					// Update old classes to new namespace
					if (strpos($mount['class'], 'OC_Filestorage_') !== false) {
						$mount['class'] = '\OC\Files\Storage\\' . substr($mount['class'], 15);
					}
					$mount['options'] = self::decryptPasswords($mount['options']);
					if (!isset($mount['priority'])) {
						$mount['priority'] = $backends[$mount['class']]['priority'];
					}
					// Remove '/$user/files/' from mount point
					$mountPoint = substr($mountPoint, 13);
					$config = array(
						'class' => $mount['class'],
						'mountpoint' => $mountPoint,
						'backend' => $backends[$mount['class']]['backend'],
						'priority' => $mount['priority'],
						'options' => $mount['options'],
						'applicable' => array('groups' => array(), 'users' => array($user))
					);
					if (isset($mount['id'])) {
						$config['id'] = (int)$mount['id'];
					}
					if (isset($mount['storage_id'])) {
						$config['storage_id'] = (int)$mount['storage_id'];
					}
					if (isset($mount['mountOptions'])) {
						$config['mountOptions'] = $mount['mountOptions'];
					}
					$hash = self::makeConfigHash($config);
					// If an existing config exists (with same class, mountpoint and options)
					if (isset($system[$hash])) {
						// add the users into that config
						$system[$hash]['applicable']['users']
							= array_merge($system[$hash]['applicable']['users'], array($user));
					} else {
						$system[$hash] = $config;
					}
				}
			}
		}
		return array_values($system);
	}

	/**
	 * Get the personal mount points of the current user
	 * The returned array is not in the same format as getUserMountPoints()
	 *
	 * @return array
	 */
	public static function getPersonalMountPoints() {
		$mountPoints = self::readData(OCP\User::getUser());
		$backEnds = self::getBackends();
		$uid = OCP\User::getUser();
		$personal = array();
		if (isset($mountPoints[self::MOUNT_TYPE_USER][$uid])) {
			foreach ($mountPoints[self::MOUNT_TYPE_USER][$uid] as $mountPoint => $mount) {
				// Update old classes to new namespace
				if (strpos($mount['class'], 'OC_Filestorage_') !== false) {
					$mount['class'] = '\OC\Files\Storage\\' . substr($mount['class'], 15);
				}
				$mount['options'] = self::decryptPasswords($mount['options']);
				$config = array(
					'class' => $mount['class'],
					// Remove '/uid/files/' from mount point
					'mountpoint' => substr($mountPoint, strlen($uid) + 8),
					'backend' => $backEnds[$mount['class']]['backend'],
					'options' => $mount['options']
				);
				if (isset($mount['id'])) {
					$config['id'] = (int)$mount['id'];
				}
				if (isset($mount['storage_id'])) {
					$config['storage_id'] = (int)$mount['storage_id'];
				}
				if (isset($mount['mountOptions'])) {
					$config['mountOptions'] = $mount['mountOptions'];
				}
				$personal[] = $config;
			}
		}
		return $personal;
	}

	/**
	 * Test connecting using the given backend configuration
	 *
	 * @param string $class backend class name
	 * @param array $options backend configuration options
	 * @return int see self::STATUS_*
	 */
	public static function getBackendStatus($class, $options, $isPersonal) {
		if (self::$skipTest) {
			return self::STATUS_SUCCESS;
		}
		foreach ($options as &$option) {
			$option = self::setUserVars(OCP\User::getUser(), $option);
		}
		if (class_exists($class)) {
			try {
				$storage = new $class($options);
				if ($storage->test($isPersonal)) {
					return self::STATUS_SUCCESS;
				}
			} catch (Exception $exception) {
				\OCP\Util::logException('files_external', $exception);
			}
		}
		return self::STATUS_ERROR;
	}

	/**
	 * Add a mount point to the filesystem
	 *
	 * @param string $mountPoint Mount point
	 * @param string $class Backend class
	 * @param array $classOptions Backend parameters for the class
	 * @param string $mountType MOUNT_TYPE_GROUP | MOUNT_TYPE_USER
	 * @param string $applicable User or group to apply mount to
	 * @param bool $isPersonal Personal or system mount point i.e. is this being called from the personal or admin page
	 * @param int|null $priority Mount point priority, null for default
	 * @return boolean
	 *
	 * @deprecated use StoragesService#addStorage() instead
	 */
	public static function addMountPoint($mountPoint,
										 $class,
										 $classOptions,
										 $mountType,
										 $applicable,
										 $isPersonal = false,
										 $priority = null) {
		$backends = self::getBackends();
		$mountPoint = OC\Files\Filesystem::normalizePath($mountPoint);
		$relMountPoint = $mountPoint;
		if ($mountPoint === '' || $mountPoint === '/') {
			// can't mount at root folder
			return false;
		}

		if (!isset($backends[$class])) {
			// invalid backend
			return false;
		}
		if ($isPersonal) {
			// Verify that the mount point applies for the current user
			// Prevent non-admin users from mounting local storage and other disabled backends
			$allowed_backends = self::getPersonalBackends();
			if ($applicable != OCP\User::getUser() || !isset($allowed_backends[$class])) {
				return false;
			}
			$mountPoint = '/' . $applicable . '/files/' . ltrim($mountPoint, '/');
		} else {
			$mountPoint = '/$user/files/' . ltrim($mountPoint, '/');
		}

		$mount = array($applicable => array(
			$mountPoint => array(
				'class' => $class,
				'options' => self::encryptPasswords($classOptions))
		)
		);
		if (!$isPersonal && !is_null($priority)) {
			$mount[$applicable][$mountPoint]['priority'] = $priority;
		}

		$mountPoints = self::readData($isPersonal ? OCP\User::getUser() : null);
		// who else loves multi-dimensional array ?
		$isNew = !isset($mountPoints[$mountType]) ||
			!isset($mountPoints[$mountType][$applicable]) ||
			!isset($mountPoints[$mountType][$applicable][$mountPoint]);
		$mountPoints = self::mergeMountPoints($mountPoints, $mount, $mountType);

		// Set default priority if none set
		if (!isset($mountPoints[$mountType][$applicable][$mountPoint]['priority'])) {
			if (isset($backends[$class]['priority'])) {
				$mountPoints[$mountType][$applicable][$mountPoint]['priority']
					= $backends[$class]['priority'];
			} else {
				$mountPoints[$mountType][$applicable][$mountPoint]['priority']
					= 100;
			}
		}

		self::writeData($isPersonal ? OCP\User::getUser() : null, $mountPoints);

		$result = self::getBackendStatus($class, $classOptions, $isPersonal);
		if ($result === self::STATUS_SUCCESS && $isNew) {
			\OC_Hook::emit(
				\OC\Files\Filesystem::CLASSNAME,
				\OC\Files\Filesystem::signal_create_mount,
				array(
					\OC\Files\Filesystem::signal_param_path => $relMountPoint,
					\OC\Files\Filesystem::signal_param_mount_type => $mountType,
					\OC\Files\Filesystem::signal_param_users => $applicable,
				)
			);
		}
		return $result;
	}

	/**
	 *
	 * @param string $mountPoint Mount point
	 * @param string $mountType MOUNT_TYPE_GROUP | MOUNT_TYPE_USER
	 * @param string $applicable User or group to remove mount from
	 * @param bool $isPersonal Personal or system mount point
	 * @return bool
	 *
	 * @deprecated use StoragesService#removeStorage() instead
	 */
	public static function removeMountPoint($mountPoint, $mountType, $applicable, $isPersonal = false) {
		// Verify that the mount point applies for the current user
		$relMountPoints = $mountPoint;
		if ($isPersonal) {
			if ($applicable != OCP\User::getUser()) {
				return false;
			}
			$mountPoint = '/' . $applicable . '/files/' . ltrim($mountPoint, '/');
		} else {
			$mountPoint = '/$user/files/' . ltrim($mountPoint, '/');
		}
		$mountPoint = \OC\Files\Filesystem::normalizePath($mountPoint);
		$mountPoints = self::readData($isPersonal ? OCP\User::getUser() : null);
		// Remove mount point
		unset($mountPoints[$mountType][$applicable][$mountPoint]);
		// Unset parent arrays if empty
		if (empty($mountPoints[$mountType][$applicable])) {
			unset($mountPoints[$mountType][$applicable]);
			if (empty($mountPoints[$mountType])) {
				unset($mountPoints[$mountType]);
			}
		}
		self::writeData($isPersonal ? OCP\User::getUser() : null, $mountPoints);
		\OC_Hook::emit(
			\OC\Files\Filesystem::CLASSNAME,
			\OC\Files\Filesystem::signal_delete_mount,
			array(
				\OC\Files\Filesystem::signal_param_path => $relMountPoints,
				\OC\Files\Filesystem::signal_param_mount_type => $mountType,
				\OC\Files\Filesystem::signal_param_users => $applicable,
			)
		);
		return true;
	}

	/**
	 *
	 * @param string $mountPoint Mount point
	 * @param string $target The new mount point
	 * @param string $mountType MOUNT_TYPE_GROUP | MOUNT_TYPE_USER
	 * @return bool
	 */
	public static function movePersonalMountPoint($mountPoint, $target, $mountType) {
		$mountPoint = rtrim($mountPoint, '/');
		$user = OCP\User::getUser();
		$mountPoints = self::readData($user);
		if (!isset($mountPoints[$mountType][$user][$mountPoint])) {
			return false;
		}
		$mountPoints[$mountType][$user][$target] = $mountPoints[$mountType][$user][$mountPoint];
		// Remove old mount point
		unset($mountPoints[$mountType][$user][$mountPoint]);

		self::writeData($user, $mountPoints);
		return true;
	}

	/**
	 * Read the mount points in the config file into an array
	 *
	 * @param string|null $user If not null, personal for $user, otherwise system
	 * @return array
	 */
	public static function readData($user = null) {
		if (isset($user)) {
			$jsonFile = OC_User::getHome($user) . '/mount.json';
		} else {
			$datadir = \OC_Config::getValue('datadirectory', \OC::$SERVERROOT . '/data/');
			$jsonFile = \OC_Config::getValue('mount_file', $datadir . '/mount.json');
		}
		if (is_file($jsonFile)) {
			$mountPoints = json_decode(file_get_contents($jsonFile), true);
			if (is_array($mountPoints)) {
				return $mountPoints;
			}
		}
		return array();
	}

	/**
	 * Write the mount points to the config file
	 *
	 * @param string|null $user If not null, personal for $user, otherwise system
	 * @param array $data Mount points
	 */
	public static function writeData($user, $data) {
		if (isset($user)) {
			$file = OC_User::getHome($user) . '/mount.json';
		} else {
			$datadir = \OC_Config::getValue('datadirectory', \OC::$SERVERROOT . '/data/');
			$file = \OC_Config::getValue('mount_file', $datadir . '/mount.json');
		}

		foreach ($data as &$applicables) {
			foreach ($applicables as &$mountPoints) {
				foreach ($mountPoints as &$options) {
					self::addStorageId($options);
				}
			}
		}

		$content = json_encode($data, JSON_PRETTY_PRINT);
		@file_put_contents($file, $content);
		@chmod($file, 0640);
	}

	/**
	 * check dependencies
	 */
	public static function checkDependencies() {
		$dependencies = array();
		foreach (OC_Mount_Config::$backends as $class => $backend) {
			if (isset($backend['has_dependencies']) and $backend['has_dependencies'] === true) {
				$result = $class::checkDependencies();
				if ($result !== true) {
					if (!is_array($result)) {
						$result = array($result);
					}
					foreach ($result as $key => $value) {
						if (is_numeric($key)) {
							OC_Mount_Config::addDependency($dependencies, $value, $backend['backend']);
						} else {
							OC_Mount_Config::addDependency($dependencies, $key, $backend['backend'], $value);
						}
					}
				}
			}
		}

		if (count($dependencies) > 0) {
			return OC_Mount_Config::generateDependencyMessage($dependencies);
		}
		return '';
	}

	private static function addDependency(&$dependencies, $module, $backend, $message = null) {
		if (!isset($dependencies[$module])) {
			$dependencies[$module] = array();
		}

		if ($message === null) {
			$dependencies[$module][] = $backend;
		} else {
			$dependencies[$module][] = array('backend' => $backend, 'message' => $message);
		}
	}

	private static function generateDependencyMessage($dependencies) {
		$l = new \OC_L10N('files_external');
		$dependencyMessage = '';
		foreach ($dependencies as $module => $backends) {
			$dependencyGroup = array();
			foreach ($backends as $backend) {
				if (is_array($backend)) {
					$dependencyMessage .= '<br />' . $l->t('<b>Note:</b> ') . $backend['message'];
				} else {
					$dependencyGroup[] = $backend;
				}
			}

			$dependencyGroupCount = count($dependencyGroup);
			if ($dependencyGroupCount > 0) {
				$backends = '';
				for ($i = 0; $i < $dependencyGroupCount; $i++) {
					if ($i > 0 && $i === $dependencyGroupCount - 1) {
						$backends .= ' ' . $l->t('and') . ' ';
					} elseif ($i > 0) {
						$backends .= ', ';
					}
					$backends .= '<i>' . $dependencyGroup[$i] . '</i>';
				}
				$dependencyMessage .= '<br />' . OC_Mount_Config::getSingleDependencyMessage($l, $module, $backends);
			}
		}
		return $dependencyMessage;
	}

	/**
	 * Returns a dependency missing message
	 *
	 * @param OC_L10N $l
	 * @param string $module
	 * @param string $backend
	 * @return string
	 */
	private static function getSingleDependencyMessage(OC_L10N $l, $module, $backend) {
		switch (strtolower($module)) {
			case 'curl':
				return $l->t('<b>Note:</b> The cURL support in PHP is not enabled or installed. Mounting of %s is not possible. Please ask your system administrator to install it.', $backend);
			case 'ftp':
				return $l->t('<b>Note:</b> The FTP support in PHP is not enabled or installed. Mounting of %s is not possible. Please ask your system administrator to install it.', $backend);
			default:
				return $l->t('<b>Note:</b> "%s" is not installed. Mounting of %s is not possible. Please ask your system administrator to install it.', array($module, $backend));
		}
	}

	/**
	 * Encrypt passwords in the given config options
	 *
	 * @param array $options mount options
	 * @return array updated options
	 */
	public static function encryptPasswords($options) {
		if (isset($options['password'])) {
			$options['password_encrypted'] = self::encryptPassword($options['password']);
			// do not unset the password, we want to keep the keys order
			// on load... because that's how the UI currently works
			$options['password'] = '';
		}
		return $options;
	}

	/**
	 * Decrypt passwords in the given config options
	 *
	 * @param array $options mount options
	 * @return array updated options
	 */
	public static function decryptPasswords($options) {
		// note: legacy options might still have the unencrypted password in the "password" field
		if (isset($options['password_encrypted'])) {
			$options['password'] = self::decryptPassword($options['password_encrypted']);
			unset($options['password_encrypted']);
		}
		return $options;
	}

	/**
	 * Encrypt a single password
	 *
	 * @param string $password plain text password
	 * @return string encrypted password
	 */
	private static function encryptPassword($password) {
		$cipher = self::getCipher();
		$iv = \OCP\Util::generateRandomBytes(16);
		$cipher->setIV($iv);
		return base64_encode($iv . $cipher->encrypt($password));
	}

	/**
	 * Decrypts a single password
	 *
	 * @param string $encryptedPassword encrypted password
	 * @return string plain text password
	 */
	private static function decryptPassword($encryptedPassword) {
		$cipher = self::getCipher();
		$binaryPassword = base64_decode($encryptedPassword);
		$iv = substr($binaryPassword, 0, 16);
		$cipher->setIV($iv);
		$binaryPassword = substr($binaryPassword, 16);
		return $cipher->decrypt($binaryPassword);
	}

	/**
	 * Merges mount points
	 *
	 * @param array $data Existing mount points
	 * @param array $mountPoint New mount point
	 * @param string $mountType
	 * @return array
	 */
	private static function mergeMountPoints($data, $mountPoint, $mountType) {
		$applicable = key($mountPoint);
		$mountPath = key($mountPoint[$applicable]);
		if (isset($data[$mountType])) {
			if (isset($data[$mountType][$applicable])) {
				// Merge priorities
				if (isset($data[$mountType][$applicable][$mountPath])
					&& isset($data[$mountType][$applicable][$mountPath]['priority'])
					&& !isset($mountPoint[$applicable][$mountPath]['priority'])
				) {
					$mountPoint[$applicable][$mountPath]['priority']
						= $data[$mountType][$applicable][$mountPath]['priority'];
				}
				$data[$mountType][$applicable]
					= array_merge($data[$mountType][$applicable], $mountPoint[$applicable]);
			} else {
				$data[$mountType] = array_merge($data[$mountType], $mountPoint);
			}
		} else {
			$data[$mountType] = $mountPoint;
		}
		return $data;
	}

	/**
	 * Returns the encryption cipher
	 */
	private static function getCipher() {
		if (!class_exists('Crypt_AES', false)) {
			include('Crypt/AES.php');
		}
		$cipher = new Crypt_AES(CRYPT_AES_MODE_CBC);
		$cipher->setKey(\OC::$server->getConfig()->getSystemValue('passwordsalt', null));
		return $cipher;
	}

	/**
	 * Computes a hash based on the given configuration.
	 * This is mostly used to find out whether configurations
	 * are the same.
	 */
	public static function makeConfigHash($config) {
		$data = json_encode(
			array(
				'c' => $config['class'],
				'm' => $config['mountpoint'],
				'o' => $config['options'],
				'p' => isset($config['priority']) ? $config['priority'] : -1,
				'mo' => isset($config['mountOptions']) ? $config['mountOptions'] : [],
			)
		);
		return hash('md5', $data);
	}

	/**
	 * Add storage id to the storage configurations that did not have any.
	 *
	 * @param string $user user for which to process storage configs
	 */
	private static function addStorageIdToConfig($user) {
		$config = self::readData($user);

		$needUpdate = false;
		foreach ($config as &$applicables) {
			foreach ($applicables as &$mountPoints) {
				foreach ($mountPoints as &$options) {
					$needUpdate |= !isset($options['storage_id']);
				}
			}
		}

		if ($needUpdate) {
			self::writeData($user, $config);
		}
	}

	/**
	 * Get storage id from the numeric storage id and set
	 * it into the given options argument. Only do this
	 * if there was no storage id set yet.
	 *
	 * This might also fail if a storage wasn't fully configured yet
	 * and couldn't be mounted, in which case this will simply return false.
	 *
	 * @param array $options storage options
	 *
	 * @return bool true if the storage id was added, false otherwise
	 */
	private static function addStorageId(&$options) {
		if (isset($options['storage_id'])) {
			return false;
		}

		$class = $options['class'];
		try {
			/** @var \OC\Files\Storage\Storage $storage */
			$storage = new $class($options['options']);
			// TODO: introduce StorageConfigException
		} catch (\Exception $e) {
			// storage might not be fully configured yet (ex: Dropbox)
			// note that storage instances aren't supposed to open any connections
			// in the constructor, so this exception is likely to be a config exception
			return false;
		}

		$options['storage_id'] = $storage->getCache()->getNumericStorageId();
		return true;
	}
}
