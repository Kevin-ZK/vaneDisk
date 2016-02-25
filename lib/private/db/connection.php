<?php

namespace OC\DB;
use Doctrine\DBAL\DBALException;
use Doctrine\DBAL\Driver;
use Doctrine\DBAL\Configuration;
use Doctrine\DBAL\Cache\QueryCacheProfile;
use Doctrine\Common\EventManager;
use OCP\IDBConnection;

class Connection extends \Doctrine\DBAL\Connection implements IDBConnection {
	/**
	 * @var string $tablePrefix
	 */
	protected $tablePrefix;

	/**
	 * @var \OC\DB\Adapter $adapter
	 */
	protected $adapter;

	public function connect() {
		try {
			return parent::connect();
		} catch (DBALException $e) {
			// throw a new exception to prevent leaking info from the stacktrace
			throw new DBALException('Failed to connect to the database: ' . $e->getMessage(), $e->getCode());
		}
	}

	/**
	 * @return string
	 */
	public function getPrefix() {
		return $this->tablePrefix;
	}

	/**
	 * Initializes a new instance of the Connection class.
	 *
	 * @param array $params  The connection parameters.
	 * @param \Doctrine\DBAL\Driver $driver
	 * @param \Doctrine\DBAL\Configuration $config
	 * @param \Doctrine\Common\EventManager $eventManager
	 * @throws \Exception
	 */
	public function __construct(array $params, Driver $driver, Configuration $config = null,
		EventManager $eventManager = null)
	{
		if (!isset($params['adapter'])) {
			throw new \Exception('adapter not set');
		}
		if (!isset($params['tablePrefix'])) {
			throw new \Exception('tablePrefix not set');
		}
		parent::__construct($params, $driver, $config, $eventManager);
		$this->adapter = new $params['adapter']($this);
		$this->tablePrefix = $params['tablePrefix'];

		parent::setTransactionIsolation(parent::TRANSACTION_READ_COMMITTED);
	}

	/**
	 * Prepares an SQL statement.
	 *
	 * @param string $statement The SQL statement to prepare.
	 * @param int $limit
	 * @param int $offset
	 * @return \Doctrine\DBAL\Driver\Statement The prepared statement.
	 */
	public function prepare( $statement, $limit=null, $offset=null ) {
		if ($limit === -1) {
			$limit = null;
		}
		if (!is_null($limit)) {
			$platform = $this->getDatabasePlatform();
			$statement = $platform->modifyLimitQuery($statement, $limit, $offset);
		}
		$statement = $this->replaceTablePrefix($statement);
		$statement = $this->adapter->fixupStatement($statement);

		if(\OC_Config::getValue( 'log_query', false)) {
			\OC_Log::write('core', 'DB prepare : '.$statement, \OC_Log::DEBUG);
		}
		return parent::prepare($statement);
	}

	/**
	 * Executes an, optionally parametrized, SQL query.
	 *
	 * If the query is parametrized, a prepared statement is used.
	 * If an SQLLogger is configured, the execution is logged.
	 *
	 * @param string                                      $query  The SQL query to execute.
	 * @param array                                       $params The parameters to bind to the query, if any.
	 * @param array                                       $types  The types the previous parameters are in.
	 * @param \Doctrine\DBAL\Cache\QueryCacheProfile|null $qcp    The query cache profile, optional.
	 *
	 * @return \Doctrine\DBAL\Driver\Statement The executed statement.
	 *
	 * @throws \Doctrine\DBAL\DBALException
	 */
	public function executeQuery($query, array $params = array(), $types = array(), QueryCacheProfile $qcp = null)
	{
		$query = $this->replaceTablePrefix($query);
		$query = $this->adapter->fixupStatement($query);
		return parent::executeQuery($query, $params, $types, $qcp);
	}

	/**
	 * Executes an SQL INSERT/UPDATE/DELETE query with the given parameters
	 * and returns the number of affected rows.
	 *
	 * This method supports PDO binding types as well as DBAL mapping types.
	 *
	 * @param string $query  The SQL query.
	 * @param array  $params The query parameters.
	 * @param array  $types  The parameter types.
	 *
	 * @return integer The number of affected rows.
	 *
	 * @throws \Doctrine\DBAL\DBALException
	 */
	public function executeUpdate($query, array $params = array(), array $types = array())
	{
		$query = $this->replaceTablePrefix($query);
		$query = $this->adapter->fixupStatement($query);
		return parent::executeUpdate($query, $params, $types);
	}

	/**
	 * Returns the ID of the last inserted row, or the last value from a sequence object,
	 * depending on the underlying driver.
	 *
	 * Note: This method may not return a meaningful or consistent result across different drivers,
	 * because the underlying database may not even support the notion of AUTO_INCREMENT/IDENTITY
	 * columns or sequences.
	 *
	 * @param string $seqName Name of the sequence object from which the ID should be returned.
	 * @return string A string representation of the last inserted ID.
	 */
	public function lastInsertId($seqName = null)
	{
		if ($seqName) {
			$seqName = $this->replaceTablePrefix($seqName);
		}
		return $this->adapter->lastInsertId($seqName);
	}

	// internal use
	public function realLastInsertId($seqName = null) {
		return parent::lastInsertId($seqName);
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
		return $this->adapter->insertIfNotExist($table, $input, $compare);
	}

	/**
	 * returns the error code and message as a string for logging
	 * works with DoctrineException
	 * @return string
	 */
	public function getError() {
		$msg = $this->errorCode() . ': ';
		$errorInfo = $this->errorInfo();
		if (is_array($errorInfo)) {
			$msg .= 'SQLSTATE = '.$errorInfo[0] . ', ';
			$msg .= 'Driver Code = '.$errorInfo[1] . ', ';
			$msg .= 'Driver Message = '.$errorInfo[2];
		}
		return $msg;
	}

	/**
	 * Drop a table from the database if it exists
	 *
	 * @param string $table table name without the prefix
	 */
	public function dropTable($table) {
		$table = $this->tablePrefix . trim($table);
		$schema = $this->getSchemaManager();
		if($schema->tablesExist(array($table))) {
			$schema->dropTable($table);
		}
	}

	/**
	 * Check if a table exists
	 *
	 * @param string $table table name without the prefix
	 * @return bool
	 */
	public function tableExists($table){
		$table = $this->tablePrefix . trim($table);
		$schema = $this->getSchemaManager();
		return $schema->tablesExist(array($table));
	}

	// internal use
	/**
	 * @param string $statement
	 * @return string
	 */
	protected function replaceTablePrefix($statement) {
		return str_replace( '*PREFIX*', $this->tablePrefix, $statement );
	}
}
