<?php

namespace OC\DB;

class OCSqlitePlatform extends \Doctrine\DBAL\Platforms\SqlitePlatform {
	/**
	 * {@inheritDoc}
	 */
	public function getColumnDeclarationSQL($name, array $field) {
		$def = parent::getColumnDeclarationSQL($name, $field);
		if (!empty($field['autoincrement'])) {
			$def .= ' PRIMARY KEY AUTOINCREMENT';
		}
		return $def;
	}

	/**
	 * {@inheritDoc}
	 */
	protected function _getCreateTableSQL($name, array $columns, array $options = array()){
		// if auto increment is set the column is already defined as primary key
		foreach ($columns as $column) {
			if (!empty($column['autoincrement'])) {
				$options['primary'] = null;
			}
		}
		return parent::_getCreateTableSQL($name, $columns, $options);
	}
}
