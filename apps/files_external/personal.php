<?php
/**
 *
 * @copyright Copyright (c) 2015, ownCloud, Inc.
 * @license AGPL-3.0
 *
 * This code is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License, version 3,
 * as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License, version 3,
 * along with this program.  If not, see <http://www.gnu.org/licenses/>
 *
 */

OCP\Util::addScript('files_external', 'settings');
OCP\Util::addStyle('files_external', 'settings');
$backends = OC_Mount_Config::getPersonalBackends();

$mounts = OC_Mount_Config::getPersonalMountPoints();
$hasId = true;
foreach ($mounts as $mount) {
	if (!isset($mount['id'])) {
		// some mount points are missing ids
		$hasId = false;
		break;
	}
}

if (!$hasId) {
	$service = new \OCA\Files_external\Service\UserStoragesService(\OC::$server->getUserSession());
	// this will trigger the new storage code which will automatically
	// generate storage config ids
	$service->getAllStorages();
	// re-read updated config
	$mounts = OC_Mount_Config::getPersonalMountPoints();
	// TODO: use the new storage config format in the template
}

$tmpl = new OCP\Template('files_external', 'settings');
$tmpl->assign('encryptionEnabled', \OC::$server->getEncryptionManager()->isEnabled());
$tmpl->assign('isAdminPage', false);
$tmpl->assign('mounts', $mounts);
$tmpl->assign('dependencies', OC_Mount_Config::checkDependencies());
$tmpl->assign('backends', $backends);
return $tmpl->fetchPage();
