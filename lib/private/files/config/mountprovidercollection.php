<?php

namespace OC\Files\Config;

use OC\Hooks\Emitter;
use OC\Hooks\EmitterTrait;
use OCP\Files\Config\IMountProviderCollection;
use OCP\Files\Config\IMountProvider;
use OCP\Files\Storage\IStorageFactory;
use OCP\IUser;

class MountProviderCollection implements IMountProviderCollection, Emitter {
	use EmitterTrait;

	/**
	 * @var \OCP\Files\Config\IMountProvider[]
	 */
	private $providers = array();

	/**
	 * @var \OCP\Files\Storage\IStorageFactory
	 */
	private $loader;

	/**
	 * @param \OCP\Files\Storage\IStorageFactory $loader
	 */
	public function __construct(IStorageFactory $loader) {
		$this->loader = $loader;
	}

	/**
	 * Get all configured mount points for the user
	 *
	 * @param \OCP\IUser $user
	 * @return \OCP\Files\Mount\IMountPoint[]
	 */
	public function getMountsForUser(IUser $user) {
		$loader = $this->loader;
		$mounts = array_map(function (IMountProvider $provider) use ($user, $loader) {
			return $provider->getMountsForUser($user, $loader);
		}, $this->providers);
		$mounts = array_filter($mounts, function ($result) {
			return is_array($result);
		});
		return array_reduce($mounts, function (array $mounts, array $providerMounts) {
			return array_merge($mounts, $providerMounts);
		}, array());
	}

	/**
	 * Add a provider for mount points
	 *
	 * @param \OCP\Files\Config\IMountProvider $provider
	 */
	public function registerProvider(IMountProvider $provider) {
		$this->providers[] = $provider;
		$this->emit('\OC\Files\Config', 'registerMountProvider', [$provider]);
	}
}
