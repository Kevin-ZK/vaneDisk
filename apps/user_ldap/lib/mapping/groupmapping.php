<?php

namespace OCA\User_LDAP\Mapping;

/**
* Class UserMapping
* @package OCA\User_LDAP\Mapping
*/
class GroupMapping extends AbstractMapping {

	/**
	* returns the DB table name which holds the mappings
	* @return string
	*/
	protected function getTableName() {
		return '*PREFIX*ldap_group_mapping';
	}

}
