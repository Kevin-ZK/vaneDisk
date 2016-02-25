<?php

namespace OC\Settings\Controller;

use OC\App\DependencyAnalyzer;
use OC\App\Platform;
use OC\OCSClient;
use OCP\App\IAppManager;
use \OCP\AppFramework\Controller;
use OCP\AppFramework\Http\ContentSecurityPolicy;
use OCP\AppFramework\Http\DataResponse;
use OCP\AppFramework\Http\TemplateResponse;
use OCP\ICacheFactory;
use OCP\INavigationManager;
use OCP\IRequest;
use OCP\IL10N;
use OCP\IConfig;

/**
 * @package OC\Settings\Controller
 */
class AppSettingsController extends Controller {

	/** @var \OCP\IL10N */
	private $l10n;
	/** @var IConfig */
	private $config;
	/** @var \OCP\ICache */
	private $cache;
	/** @var INavigationManager */
	private $navigationManager;
	/** @var IAppManager */
	private $appManager;
	/** @var OCSClient */
	private $ocsClient;

	/**
	 * @param string $appName
	 * @param IRequest $request
	 * @param IL10N $l10n
	 * @param IConfig $config
	 * @param ICacheFactory $cache
	 * @param INavigationManager $navigationManager
	 * @param IAppManager $appManager
	 * @param OCSClient $ocsClient
	 */
	public function __construct($appName,
								IRequest $request,
								IL10N $l10n,
								IConfig $config,
								ICacheFactory $cache,
								INavigationManager $navigationManager,
								IAppManager $appManager,
								OCSClient $ocsClient) {
		parent::__construct($appName, $request);
		$this->l10n = $l10n;
		$this->config = $config;
		$this->cache = $cache->create($appName);
		$this->navigationManager = $navigationManager;
		$this->appManager = $appManager;
		$this->ocsClient = $ocsClient;
	}

	/**
	 * Enables or disables the display of experimental apps
	 * @param bool $state
	 * @return DataResponse
	 */
	public function changeExperimentalConfigState($state) {
		$this->config->setSystemValue('appstore.experimental.enabled', $state);
		$this->appManager->clearAppsCache();
		return new DataResponse();
	}

	/**
	 * @NoCSRFRequired
	 * @return TemplateResponse
	 */
	public function viewApps() {
		$params = [];
		$params['experimentalEnabled'] = $this->config->getSystemValue('appstore.experimental.enabled', false);
		$this->navigationManager->setActiveEntry('core_apps');

		$templateResponse = new TemplateResponse($this->appName, 'apps', $params, 'user');
		$policy = new ContentSecurityPolicy();
		$policy->addAllowedImageDomain('https://apps.owncloud.com');
		$templateResponse->setContentSecurityPolicy($policy);

		return $templateResponse;
	}

	/**
	 * Get all available categories
	 * @return array
	 */
	public function listCategories() {

		if(!is_null($this->cache->get('listCategories'))) {
			return $this->cache->get('listCategories');
		}
		$categories = [
			['id' => 0, 'displayName' => (string)$this->l10n->t('Enabled')],
			['id' => 1, 'displayName' => (string)$this->l10n->t('Not enabled')],
		];

		if($this->ocsClient->isAppStoreEnabled()) {
			// apps from external repo via OCS
			$ocs = $this->ocsClient->getCategories(\OC_Util::getVersion());
			if ($ocs) {
				foreach($ocs as $k => $v) {
					$categories[] = [
						'id' => $k,
						'displayName' => str_replace('ownCloud ', '', $v)
					];
				}
			}
		}

		$this->cache->set('listCategories', $categories, 3600);

		return $categories;
	}

	/**
	 * Get all available apps in a category
	 *
	 * @param int $category
	 * @param bool $includeUpdateInfo Should we check whether there is an update
	 *                                in the app store?
	 * @return array
	 */
	public function listApps($category = 0, $includeUpdateInfo = true) {
		$cacheName = 'listApps-' . $category . '-' . (int) $includeUpdateInfo;

		if(!is_null($this->cache->get($cacheName))) {
			$apps = $this->cache->get($cacheName);
		} else {
			switch ($category) {
				// installed apps
				case 0:
					$apps = $this->getInstalledApps($includeUpdateInfo);
					usort($apps, function ($a, $b) {
						$a = (string)$a['name'];
						$b = (string)$b['name'];
						if ($a === $b) {
							return 0;
						}
						return ($a < $b) ? -1 : 1;
					});
					foreach($apps as $key => $app) {
						if(!array_key_exists('level', $app) && array_key_exists('ocsid', $app)) {
							$remoteAppEntry = $this->ocsClient->getApplication($app['ocsid'], \OC_Util::getVersion());

							if(is_array($remoteAppEntry) && array_key_exists('level', $remoteAppEntry)) {
								$apps[$key]['level'] = $remoteAppEntry['level'];
							}
						}
					}
					break;
				// not-installed apps
				case 1:
					$apps = \OC_App::listAllApps(true, $includeUpdateInfo);
					$apps = array_filter($apps, function ($app) {
						return !$app['active'];
					});
					foreach($apps as $key => $app) {
						if(!array_key_exists('level', $app) && array_key_exists('ocsid', $app)) {
							$remoteAppEntry = $this->ocsClient->getApplication($app['ocsid'], \OC_Util::getVersion());

							if(is_array($remoteAppEntry) && array_key_exists('level', $remoteAppEntry)) {
								$apps[$key]['level'] = $remoteAppEntry['level'];
							}
						}
					}
					usort($apps, function ($a, $b) {
						$a = (string)$a['name'];
						$b = (string)$b['name'];
						if ($a === $b) {
							return 0;
						}
						return ($a < $b) ? -1 : 1;
					});
					break;
				default:
					$filter = $this->config->getSystemValue('appstore.experimental.enabled', false) ? 'all' : 'approved';

					$apps = \OC_App::getAppstoreApps($filter, $category);
					if (!$apps) {
						$apps = array();
					} else {
						// don't list installed apps
						$installedApps = $this->getInstalledApps(false);
						$installedApps = array_map(function ($app) {
							if (isset($app['ocsid'])) {
								return $app['ocsid'];
							}
							return $app['id'];
						}, $installedApps);
						$apps = array_filter($apps, function ($app) use ($installedApps) {
							return !in_array($app['id'], $installedApps);
						});
					}

					// sort by score
					usort($apps, function ($a, $b) {
						$a = (int)$a['score'];
						$b = (int)$b['score'];
						if ($a === $b) {
							return 0;
						}
						return ($a > $b) ? -1 : 1;
					});
					break;
			}
		}

		// fix groups to be an array
		$dependencyAnalyzer = new DependencyAnalyzer(new Platform($this->config), $this->l10n);
		$apps = array_map(function($app) use ($dependencyAnalyzer) {

			// fix groups
			$groups = array();
			if (is_string($app['groups'])) {
				$groups = json_decode($app['groups']);
			}
			$app['groups'] = $groups;
			$app['canUnInstall'] = !$app['active'] && $app['removable'];

			// fix licence vs license
			if (isset($app['license']) && !isset($app['licence'])) {
				$app['licence'] = $app['license'];
			}

			// analyse dependencies
			$missing = $dependencyAnalyzer->analyze($app);
			$app['canInstall'] = empty($missing);
			$app['missingDependencies'] = $missing;

			return $app;
		}, $apps);

		$this->cache->set($cacheName, $apps, 300);

		return ['apps' => $apps, 'status' => 'success'];
	}

	/**
	 * @param bool $includeUpdateInfo Should we check whether there is an update
	 *                                in the app store?
	 * @return array
	 */
	private function getInstalledApps($includeUpdateInfo = true) {
		$apps = \OC_App::listAllApps(true, $includeUpdateInfo);
		$apps = array_filter($apps, function ($app) {
			return $app['active'];
		});
		return $apps;
	}
}
