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

try {

	require_once 'lib/base.php';

	$systemConfig = \OC::$server->getSystemConfig();

	$installed = $systemConfig->getValue('installed') == 1;
	$maintenance = $systemConfig->getValue('maintenance', false);
	$values=array(
		'installed'=>$installed,
		'maintenance' => $maintenance,
		'version'=>implode('.', OC_Util::getVersion()),
		'versionstring'=>OC_Util::getVersionString(),
		'edition'=>OC_Util::getEditionString());
	if (OC::$CLI) {
		print_r($values);
	} else {
		header('Access-Control-Allow-Origin: *');
		header('Content-Type: application/json');
		echo json_encode($values);
	}

} catch (Exception $ex) {
	OC_Response::setStatus(OC_Response::STATUS_INTERNAL_SERVER_ERROR);
	\OCP\Util::writeLog('remote', $ex->getMessage(), \OCP\Util::FATAL);
}
