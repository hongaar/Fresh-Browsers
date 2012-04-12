<?php
/**
 * PDO Wrapper is part of Nanobanano framework
 *
 * PHP version 5
 *
 * @copyright 2012 Dmitry Elfimov
 * @license   http://www.elfimov.ru/nanobanano/license.txt MIT License
 * @link      http://elfimov.ru/nanobanano
 * 
 */
 
/**
 * PDOWrapperStatement class
 *
 * @package PDOWrapper
 * @author  Dmitry Elfimov <elfimov@gmail.com>
 *
 */
 
class PDOWrapperStatement
{

    private $_pdo = null;
    private $_statement = null;
    public $error = false;
    
    /**
     * Constructor.
     *
     * @param PDO $pdo PDO object.
     */
    public function __construct($pdo)
    {
        $this->_pdo = $pdo;
    }
    
    /**
     * Prepares a statement for execution and returns a statement object.
     *
     * @param string $query a valid SQL statement for the target db
     *
     * @return PDOWrapperStatement object
     */
    public function prepare($query)
    {
        $this->_statement = $this->_pdo->prepare($query);
        return $this;
    }

    /**
     * Prepares a statement for execution and returns a statement object.
     * See php.net/manual/pdostatement.bindvalue.php
     *
     * @param string $pos   parameter identifier
     * @param string $value the value to bind to the parameter
     * @param string $type  data type
     *
     * @return PDOWrapperStatement object
     */
    public function bind($pos, $value, $type = null) 
    {

        if (is_null($type)) {
            switch (true) {
            case is_int($value):
                $type = PDO::PARAM_INT;
                break;
            case is_bool($value):
                $type = PDO::PARAM_BOOL;
                break;
            case is_null($value):
                $type = PDO::PARAM_NULL;
                break;
            default:
                $type = PDO::PARAM_STR;
            }
        }

        $this->_statement->bindValue($pos, $value, $type);
        return $this;
    }
    
    /**
     * Executes a prepared statement.
     * See php.net/manual/pdostatement.execute.php
     *
     * @param string $parameters array of values like in bind()
     *
     * @return PDOWrapperStatement object
     */
    public function execute(array $parameters=null) 
    {
        $this->error = !$this->_statement->execute($parameters);
        return $this;
    }
    
    /**
     * Fetch the SQLSTATE associated with the last operation.
     * See php.net/manual/pdo.errorcode.php
     *
     * @return SQLSTATE
     */
    public function errorCode() 
    {
        return $this->_statement->errorCode();
    }
    
    /**
     * Fetch extended error information.
     * See php.net/manual/pdo.errorinfo.php
     *
     * @return an array of error information about the last operation.
     */
    public function errorInfo() 
    {
        return $this->_statement->errorInfo();
    }
    
    /**
     * Fetches the next row from a result set.
     * See php.net/manual/pdostatement.fetch.php
     *
     * @param string $fetchStyle controls how the next row will be returned, 
     * must be one of the PDO::FETCH_*
     *
     * @return depends on the fetch type, false is returned on failure.
     */
    public function fetch($fetchStyle=PDO::FETCH_ASSOC) 
    {
        return $this->_statement->fetch($fetchStyle);
    }
    
    /**
     * Fetches the next row from a result set as object.
     * See php.net/manual/pdostatement.fetch.php
     *
     * @return object.
     */
    public function fetchObject() 
    {
        return $this->fetch(PDO::FETCH_OBJ);
    }
    
    /**
     * Returns an array containing all of the result set rows.
     * See php.net/manual/pdostatement.fetchall.php
     *
     * @param string $fetchStyle controls how the next row will be returned, 
     * must be one of the PDO::FETCH_*
     *
     * @return depends on the fetch type.
     */
    public function fetchAll($fetchStyle=PDO::FETCH_ASSOC) 
    {
        return $this->_statement->fetchAll($fetchStyle);
    }
    
    /**
     * Returns the number of rows affected by the last SQL statement.
     * See php.net/manual/pdostatement.rowcount.php
     *
     * @return the number of rows.
     */
    public function rowCount() 
    {
        return $this->_statement->rowCount();
    }

    /**
     * Returns the number of columns in the result set.
     * See php.net/manual/pdostatement.columncount.php
     *
     * @return the number of columns in the result set.
     */
    public function columnCount() 
    {
        return $this->_statement->columnCount();
    }


}