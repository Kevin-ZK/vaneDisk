<?php

namespace OCA\Files_Sharing;

class Migration {


	/**
	 * set accepted to 1 for all external shares. At this point in time we only
	 * have shares from the first version of server-to-server sharing so all should
	 * be accepted
	 */
	public function addAcceptRow() {
		$statement = 'UPDATE `*PREFIX*share_external` SET `accepted` = 1';
		$connection = \OC::$server->getDatabaseConnection();
		$query = $connection->prepare($statement);
		$query->execute();
	}


}
