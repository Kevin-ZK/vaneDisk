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

namespace OCA\Files_External\Appinfo;

use \OCA\Files_External\Controller\AjaxController;
use \OCP\AppFramework\App;
use \OCP\IContainer;

/**
 * @package OCA\Files_External\Appinfo
 */
class Application extends App {
	public function __construct(array $urlParams=array()) {
		parent::__construct('files_external', $urlParams);

		$container = $this->getContainer();

		/**
		 * Controllers
		 */
		$container->registerService('AjaxController', function (IContainer $c) {
			return new AjaxController(
				$c->query('AppName'),
				$c->query('Request')
			);
		});
	}
}
