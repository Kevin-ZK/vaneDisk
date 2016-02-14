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

/** @var $application Symfony\Component\Console\Application */
$application->add(new OC\Core\Command\Status);
$application->add(new OC\Core\Command\Check(\OC::$server->getConfig()));
$application->add(new OC\Core\Command\App\CheckCode());
$application->add(new OC\Core\Command\L10n\CreateJs());

if (\OC::$server->getConfig()->getSystemValue('installed', false)) {
	$repair = new \OC\Repair(\OC\Repair::getRepairSteps());

	$application->add(new OC\Core\Command\Db\GenerateChangeScript());
	$application->add(new OC\Core\Command\Db\ConvertType(\OC::$server->getConfig(), new \OC\DB\ConnectionFactory()));
	$application->add(new OC\Core\Command\Upgrade(\OC::$server->getConfig()));
	$application->add(new OC\Core\Command\Maintenance\SingleUser());
	$application->add(new OC\Core\Command\Maintenance\Mode(\OC::$server->getConfig()));
	$application->add(new OC\Core\Command\App\Disable());
	$application->add(new OC\Core\Command\App\Enable());
	$application->add(new OC\Core\Command\App\ListApps());
	$application->add(new OC\Core\Command\Maintenance\Repair($repair, \OC::$server->getConfig()));
	$application->add(new OC\Core\Command\User\Add(\OC::$server->getUserManager(), \OC::$server->getGroupManager()));
	$application->add(new OC\Core\Command\User\Delete(\OC::$server->getUserManager()));
	$application->add(new OC\Core\Command\User\LastSeen(\OC::$server->getUserManager()));
	$application->add(new OC\Core\Command\User\Report(\OC::$server->getUserManager()));
	$application->add(new OC\Core\Command\User\ResetPassword(\OC::$server->getUserManager()));
	$application->add(new OC\Core\Command\Background\Cron(\OC::$server->getConfig()));
	$application->add(new OC\Core\Command\Background\WebCron(\OC::$server->getConfig()));
	$application->add(new OC\Core\Command\Background\Ajax(\OC::$server->getConfig()));
	$application->add(new OC\Core\Command\Encryption\Disable(\OC::$server->getConfig()));
	$application->add(new OC\Core\Command\Encryption\Enable(\OC::$server->getConfig(), \OC::$server->getEncryptionManager()));
	$application->add(new OC\Core\Command\Encryption\ListModules(\OC::$server->getEncryptionManager()));
	$application->add(new OC\Core\Command\Encryption\SetDefaultModule(\OC::$server->getEncryptionManager()));
	$application->add(new OC\Core\Command\Encryption\Status(\OC::$server->getEncryptionManager()));
} else {
	$application->add(new OC\Core\Command\Maintenance\Install(\OC::$server->getConfig()));
}
