<?php

namespace OCA\Files_Sharing;

use OC\Files\Filesystem;
use OC\User\NoUserException;
use OCA\Files_Sharing\Propagation\PropagationManager;
use OCP\Files\Config\IMountProvider;
use OCP\Files\Storage\IStorageFactory;
use OCP\IConfig;
use OCP\IUser;

class MountProvider implements IMountProvider {
	/**
	 * @var \OCP\IConfig
	 */
	protected $config;

	/**
	 * @var \OCA\Files_Sharing\Propagation\PropagationManager
	 */
	protected $propagationManager;

	/**
	 * @param \OCP\IConfig $config
	 * @param \OCA\Files_Sharing\Propagation\PropagationManager $propagationManager
	 */
	public function __construct(IConfig $config, PropagationManager $propagationManager) {
		$this->config = $config;
		$this->propagationManager = $propagationManager;
	}


	/**
	 * Get all mountpoints applicable for the user and check for shares where we need to update the etags
	 *
	 * @param \OCP\IUser $user
	 * @param \OCP\Files\Storage\IStorageFactory $storageFactory
	 * @return \OCP\Files\Mount\IMountPoint[]
	 */
	public function getMountsForUser(IUser $user, IStorageFactory $storageFactory) {
		$shares = \OCP\Share::getItemsSharedWithUser('file', $user->getUID());
		$propagator = $this->propagationManager->getSharePropagator($user->getUID());
		$propagator->propagateDirtyMountPoints($shares);
		$shares = array_filter($shares, function ($share) {
			return $share['permissions'] > 0;
		});
		$shares = array_map(function ($share) use ($user, $storageFactory) {
			// for updating etags for the share owner when we make changes to this share.
			$ownerPropagator = $this->propagationManager->getChangePropagator($share['uid_owner']);

			// for updating our etags when changes are made to the share from the owners side (probably indirectly by us trough another share)
			$this->propagationManager->listenToOwnerChanges($share['uid_owner'], $user->getUID());
			return new SharedMount(
				'\OC\Files\Storage\Shared',
				'/' . $user->getUID() . '/' . $share['file_target'],
				array(
					'propagator' => $ownerPropagator,
					'share' => $share,
					'user' => $user->getUID()
				),
				$storageFactory
			);
		}, $shares);
		// array_filter removes the null values from the array
		return array_filter($shares);
	}
}
