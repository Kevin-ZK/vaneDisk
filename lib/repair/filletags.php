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

namespace OC\Repair;

use Doctrine\DBAL\Query\QueryBuilder;
use OC\Hooks\BasicEmitter;

class FillETags extends BasicEmitter implements \OC\RepairStep {

	/** @var \OC\DB\Connection */
	protected $connection;

	/**
	 * @param \OC\DB\Connection $connection
	 */
	public function __construct($connection) {
		$this->connection = $connection;
	}

	public function getName() {
		return 'Generate ETags for file where no ETag is present.';
	}

	public function run() {
		$qb = $this->connection->createQueryBuilder();
		$qb->update('`*PREFIX*filecache`')
			->set('`etag`', $qb->expr()->literal('xxx'))
			->where($qb->expr()->eq('`etag`', $qb->expr()->literal('')))
			->orWhere($qb->expr()->isNull('`etag`'));

		$result = $qb->execute();
		$this->emit('\OC\Repair', 'info', array("ETags have been fixed for $result files/folders."));
	}
}

