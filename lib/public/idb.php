<?php

namespace OCP;


/**
 * Small Facade for being able to inject the database connection for tests
 * @since 7.0.0 - extends IDBConnection was added in 8.1.0
 */
interface IDb extends IDBConnection {


    /**
     * Used to abstract the owncloud database access away
     * @param string $sql the sql query with ? placeholder for params
     * @param int $limit the maximum number of rows
     * @param int $offset from which row we want to start
     * @return \OC_DB_StatementWrapper prepared SQL query
	 * @since 7.0.0
     */
    public function prepareQuery($sql, $limit=null, $offset=null);


    /**
     * Used to get the id of the just inserted element
     * @param string $tableName the name of the table where we inserted the item
     * @return int the id of the inserted element
	 * @since 7.0.0
     */
    public function getInsertId($tableName);


}
