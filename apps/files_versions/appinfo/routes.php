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

/** @var $this \OCP\Route\IRouter */
$this->create('core_ajax_versions_preview', '/preview')->action(
function() {
	require_once __DIR__ . '/../ajax/preview.php';
});

$this->create('files_versions_download', 'download.php')
	->actionInclude('files_versions/download.php');
$this->create('files_versions_ajax_getVersions', 'ajax/getVersions.php')
	->actionInclude('files_versions/ajax/getVersions.php');
$this->create('files_versions_ajax_rollbackVersion', 'ajax/rollbackVersion.php')
	->actionInclude('files_versions/ajax/rollbackVersion.php');

// Register with the capabilities API
\OCP\API::register('get', '/cloud/capabilities', array('OCA\Files_Versions\Capabilities', 'getCapabilities'), 'files_versions', \OCP\API::USER_AUTH);
