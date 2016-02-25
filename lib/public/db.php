<?php
namespace OCP;

/**
 * This class provides access to the internal database system. Use this class exlusively if you want to access databases
 * @deprecated 8.1.0 use methods of \OCP\IDBConnection - \OC::$server->getDatabaseConnection()
 * @since 4.5.0
 */
class DB {
	/**
	 * Prepare a SQL query
	 * @param string $query Query string
	 * @param int $limit Limit of the SQL statement
	 * @param int $offset Offset of the SQL statement
	 * @return \OC_DB_StatementWrapper prepared SQL query
	 *
	 * SQL query via Doctrine prepare(), needs to be execute()'d!
	 * @deprecated 8.1.0 use prepare() of \OCP\IDBConnection - \OC::$server->getDatabaseConnection()
	 * @since 4.5.0
	 */
	static public function prepare( $query, $limit=null, $offset=null ) {
		return(\OC_DB::prepare($query, $limit, $offset));
	}

	/**
	 * Insert a row if the matching row does not exists.
	 *
	 * @param string $table The table name (will replace *PREFIX* with the actual prefix)
	 * @param array $input data that should be inserted into the table  (column name => value)
	 * @param array|null $compare List of values that should be checked for "if not exists"
	 *				If this is null or an empty array, all keys of $input will be compared
	 * @return int number of inserted rows
	 * @throws \Doctrine\DBAL\DBALException
	 * @deprecated 8.1.0 use insertIfNotExist() of \OCP\IDBConnection - \OC::$server->getDatabaseConnection()
	 * @since 5.0.0 - parameter $compare was added in 8.1.0
	 *
	 */
	public static function insertIfNotExist($table, $input, array $compare = null) {
		return \OC::$server->getDatabaseConnection()->insertIfNotExist($table, $input, $compare);
	}

	/**
	 * Gets last value of autoincrement
	 * @param string $table The optional table name (will replace *PREFIX*) and add sequence suffix
	 * @return string
	 *
	 * \Doctrine\DBAL\Connection lastInsertID()
	 *
	 * Call this method right after the insert command or other functions may
	 * cause trouble!
	 * @deprecated 8.1.0 use lastInsertId() of \OCP\IDBConnection - \OC::$server->getDatabaseConnection()
	 * @since 4.5.0
	 */
	public static function insertid($table=null) {
		return \OC::$server->getDatabaseConnection()->lastInsertId($table);
	}

	/**
	 * Start a transaction
	 * @deprecated 8.1.0 use beginTransaction() of \OCP\IDBConnection - \OC::$server->getDatabaseConnection()
	 * @since 4.5.0
	 */
	public static function beginTransaction() {
		\OC::$server->getDatabaseConnection()->beginTransaction();
	}

	/**
	 * Commit the database changes done during a transaction that is in progress
	 * @deprecated 8.1.0 use commit() of \OCP\IDBConnection - \OC::$server->getDatabaseConnection()
	 * @since 4.5.0
	 */
	public static function commit() {
		\OC::$server->getDatabaseConnection()->commit();
	}

	/**
	 * Rollback the database changes done during a transaction that is in progress
	 * @deprecated 8.1.0 use rollback() of \OCP\IDBConnection - \OC::$server->getDatabaseConnection()
	 * @since 8.0.0
	 */
	public static function rollback() {
		\OC::$server->getDatabaseConnection()->rollback();
	}

	/**
	 * Check if a result is an error, works with Doctrine
	 * @param mixed $result
	 * @return bool
	 * @deprecated 8.1.0 Doctrine returns false on error (and throws an exception)
	 * @since 4.5.0
	 */
	public static function isError($result) {
		// Doctrine returns false on error (and throws an exception)
		return $result === false;
	}

	/**
	 * returns the error code and message as a string for logging
	 * works with DoctrineException
	 * @return string
	 * @deprecated 8.1.0 use getError() of \OCP\IDBConnection - \OC::$server->getDatabaseConnection()
	 * @since 6.0.0
	 */
	public static function getErrorMessage() {
		return \OC::$server->getDatabaseConnection()->getError();
	}

}
