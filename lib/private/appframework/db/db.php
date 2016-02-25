<?php

namespace OC\AppFramework\Db;

use OCP\IDb;
use OCP\IDBConnection;

/**
 * @deprecated use IDBConnection directly, will be removed
 * Small Facade for being able to inject the database connection for tests
 */
class Db implements IDb {
	/**
	 * @var IDBConnection
	 */
	protected $connection;

	/**
	 * @param IDBConnection $connection
	 */
	public function __construct(IDBConnection $connection) {
		$this->connection = $connection;
	}

	/**
	 * Used to abstract the ownCloud database access away
	 *
	 * @param string $sql the sql query with ? placeholder for params
	 * @param int $limit the maximum number of rows
	 * @param int $offset from which row we want to start
	 * @deprecated use prepare instead, will be removed in ownCloud 10
	 * @return \OC_DB_StatementWrapper prepared SQL query
	 */
	public function prepareQuery($sql, $limit = null, $offset = null) {
		$isManipulation = \OC_DB::isManipulation($sql);
		$statement = $this->connection->prepare($sql, $limit, $offset);
		return new \OC_DB_StatementWrapper($statement, $isManipulation);
	}


	/**
	 * Used to get the id of the just inserted element
	 *
	 * @deprecated use lastInsertId instead, will be removed in ownCloud 10
	 * @param string $tableName the name of the table where we inserted the item
	 * @return int the id of the inserted element
	 */
	public function getInsertId($tableName) {
		return $this->connection->lastInsertId($tableName);
	}

	/**
	 * Used to abstract the ownCloud database access away
	 * @param string $sql the sql query with ? placeholder for params
	 * @param int $limit the maximum number of rows
	 * @param int $offset from which row we want to start
	 * @return \Doctrine\DBAL\Driver\Statement The prepared statement.
	 */
	public function prepare($sql, $limit=null, $offset=null) {
		return $this->connection->prepare($sql, $limit, $offset);
	}

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
	 */
	public function executeQuery($query, array $params = array(), $types = array()) {
		return $this->connection->executeQuery($query, $params, $types);
	}

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
	 */
	public function executeUpdate($query, array $params = array(), array $types = array()) {
		return $this->connection->executeUpdate($query, $params, $types);
	}

	/**
	 * Used to get the id of the just inserted element
	 * @param string $table the name of the table where we inserted the item
	 * @return int the id of the inserted element
	 */
	public function lastInsertId($table = null) {
		return $this->connection->lastInsertId($table);
	}

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
	 */
	public function insertIfNotExist($table, $input, array $compare = null) {
		return $this->connection->insertIfNotExist($table, $input, $compare);
	}

	/**
	 * Start a transaction
	 */
	public function beginTransaction() {
		$this->connection->beginTransaction();
	}

	/**
	 * Commit the database changes done during a transaction that is in progress
	 */
	public function commit() {
		$this->connection->commit();
	}

	/**
	 * Rollback the database changes done during a transaction that is in progress
	 */
	public function rollBack() {
		$this->connection->rollBack();
	}

	/**
	 * Gets the error code and message as a string for logging
	 * @return string
	 */
	public function getError() {
		return $this->connection->getError();
	}

	/**
	 * Fetch the SQLSTATE associated with the last database operation.
	 *
	 * @return integer The last error code.
	 */
	public function errorCode() {
		return $this->connection->errorCode();
	}

	/**
	 * Fetch extended error information associated with the last database operation.
	 *
	 * @return array The last error information.
	 */
	public function errorInfo() {
		return $this->connection->errorInfo();
	}

	/**
	 * Establishes the connection with the database.
	 *
	 * @return bool
	 */
	public function connect() {
		return $this->connection->connect();
	}

	/**
	 * Close the database connection
	 */
	public function close() {
		$this->connection->close();
	}

	/**
	 * Quotes a given input parameter.
	 *
	 * @param mixed $input Parameter to be quoted.
	 * @param int $type Type of the parameter.
	 * @return string The quoted parameter.
	 */
	public function quote($input, $type = \PDO::PARAM_STR) {
		return $this->connection->quote($input, $type);
	}

	/**
	 * Gets the DatabasePlatform instance that provides all the metadata about
	 * the platform this driver connects to.
	 *
	 * @return \Doctrine\DBAL\Platforms\AbstractPlatform The database platform.
	 */
	public function getDatabasePlatform() {
		return $this->connection->getDatabasePlatform();
	}

	/**
	 * Drop a table from the database if it exists
	 *
	 * @param string $table table name without the prefix
	 */
	public function dropTable($table) {
		$this->connection->dropTable($table);
	}

	/**
	 * Check if a table exists
	 *
	 * @param string $table table name without the prefix
	 * @return bool
	 */
	public function tableExists($table) {
		return $this->connection->tableExists($table);
	}

}
