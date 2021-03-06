<?php
namespace OCP;

/**
 * Interface IDBConnection
 *
 * @package OCP
 * @since 6.0.0
 */
interface IDBConnection {
	/**
	 * Used to abstract the vanedisk database access away
	 * @param string $sql the sql query with ? placeholder for params
	 * @param int $limit the maximum number of rows
	 * @param int $offset from which row we want to start
	 * @return \Doctrine\DBAL\Driver\Statement The prepared statement.
	 * @since 6.0.0
	 */
	public function prepare($sql, $limit=null, $offset=null);

	/**
	 * Executes an, optionally parameterized, SQL query.
	 *
	 * If the query is parameterized, a prepared statement is used.
	 * If an SQLLogger is configured, the execution is logged.
	 *
	 * @param string $query The SQL query to execute.
	 * @param string[] $params The parameters to bind to the query, if any.
	 * @param array $types The types the previous parameters are in.
	 * @return \Doctrine\DBAL\Driver\Statement The executed statement.
	 * @since 8.0.0
	 */
	public function executeQuery($query, array $params = array(), $types = array());

	/**
	 * Executes an SQL INSERT/UPDATE/DELETE query with the given parameters
	 * and returns the number of affected rows.
	 *
	 * This method supports PDO binding types as well as DBAL mapping types.
	 *
	 * @param string $query The SQL query.
	 * @param array $params The query parameters.
	 * @param array $types The parameter types.
	 * @return integer The number of affected rows.
	 * @since 8.0.0
	 */
	public function executeUpdate($query, array $params = array(), array $types = array());

	/**
	 * Used to get the id of the just inserted element
	 * @param string $table the name of the table where we inserted the item
	 * @return int the id of the inserted element
	 * @since 6.0.0
	 */
	public function lastInsertId($table = null);

	/**
	 * Insert a row if the matching row does not exists.
	 *
	 * @param string $table The table name (will replace *PREFIX* with the actual prefix)
	 * @param array $input data that should be inserted into the table  (column name => value)
	 * @param array|null $compare List of values that should be checked for "if not exists"
	 *				If this is null or an empty array, all keys of $input will be compared
	 *				Please note: text fields (clob) must not be used in the compare array
	 * @return int number of inserted rows
	 * @throws \Doctrine\DBAL\DBALException
	 * @since 6.0.0 - parameter $compare was added in 8.1.0, return type changed from boolean in 8.1.0
	 */
	public function insertIfNotExist($table, $input, array $compare = null);

	/**
	 * Start a transaction
	 * @since 6.0.0
	 */
	public function beginTransaction();

	/**
	 * Commit the database changes done during a transaction that is in progress
	 * @since 6.0.0
	 */
	public function commit();

	/**
	 * Rollback the database changes done during a transaction that is in progress
	 * @since 6.0.0
	 */
	public function rollBack();

	/**
	 * Gets the error code and message as a string for logging
	 * @return string
	 * @since 6.0.0
	 */
	public function getError();

	/**
	 * Fetch the SQLSTATE associated with the last database operation.
	 *
	 * @return integer The last error code.
	 * @since 8.0.0
	 */
	public function errorCode();

	/**
	 * Fetch extended error information associated with the last database operation.
	 *
	 * @return array The last error information.
	 * @since 8.0.0
	 */
	public function errorInfo();

	/**
	 * Establishes the connection with the database.
	 *
	 * @return bool
	 * @since 8.0.0
	 */
	public function connect();

	/**
	 * Close the database connection
	 * @since 8.0.0
	 */
	public function close();

	/**
	 * Quotes a given input parameter.
	 *
	 * @param mixed $input Parameter to be quoted.
	 * @param int $type Type of the parameter.
	 * @return string The quoted parameter.
	 * @since 8.0.0
	 */
	public function quote($input, $type = \PDO::PARAM_STR);

	/**
	 * Gets the DatabasePlatform instance that provides all the metadata about
	 * the platform this driver connects to.
	 *
	 * @return \Doctrine\DBAL\Platforms\AbstractPlatform The database platform.
	 * @since 8.0.0
	 */
	public function getDatabasePlatform();

	/**
	 * Drop a table from the database if it exists
	 *
	 * @param string $table table name without the prefix
	 * @since 8.0.0
	 */
	public function dropTable($table);

	/**
	 * Check if a table exists
	 *
	 * @param string $table table name without the prefix
	 * @return bool
	 * @since 8.0.0
	 */
	public function tableExists($table);
}
