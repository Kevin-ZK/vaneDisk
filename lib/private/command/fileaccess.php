<?php

namespace OC\Command;

use OCP\IUser;

trait FileAccess {
	protected function setupFS(IUser $user){
		\OC_Util::setupFS($user->getUID());
	}

	protected function getUserFolder(IUser $user) {
		$this->setupFS($user);
		return \OC::$server->getUserFolder($user->getUID());
	}
}
