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
 * PDOWrapper class
 *
 * @package PDOWrapper
 * @author  Dmitry Elfimov <elfimov@gmail.com>
 *
 * Example:
// Establish a connection.
$db = new DB('user', 'password', 'database');

// Create query, bind values and return a single row.
$row = $db->prepare('SELECT col1, col2, col3 FROM mytable WHERE id > ? LIMIT ?')
    ->bind(1, 2)
    ->bind(2, 1)
    ->execute()
    ->fetchAll();

// Update the LIMIT and get a resultset.
$db->bind(2,2);
$res = $db->execute()->fetchAll();

// Create a new query, bind values and return a resultset.
$res = $db->prepare('SELECT col1, col2, col3 FROM mytable WHERE col2 = ?')
    ->bind(1, 'abc')
    ->execute()
    ->fetchAll();

// Update WHERE clause and return a resultset.
$db->bind(1, 'def')->execute()->fetchAll();


$res = $this->db->prepare('SELECT * FROM foobar WHERE weight>:weight')
    ->bind(':weight', 30)
    ->execute();
while ($row = $res->fetch()) {
    // do something
}

*/


class PDOWrapper
{

    public $handler;
    public $stmt;
    public $pager;

    /**
     * Constructor.
     *
     * @param PDO $pdo PDO object.
     */
    public function __construct($pdo) 
    {
        $this->handler = $pdo;
        include_once dirname(__FILE__).'/PDOWrapperStatement.php';
    }

    /**
     * Prepares a statement for execution and returns a statement object.
     *
     * @param string $query a valid SQL statement for the target db
     *
     * @return PDOStatement object
     */
    public function prepare($query) 
    {
        $stmt = new PDOWrapperStatement($this->handler);
        $stmt->prepare($query);
        return $stmt;
    }
    
    public function preparePager($query, $options) 
    {
        include_once dirname(__FILE__).'/PagerStatement.php';
        $pager = new PagerStatement($this->handler, $options);
        $pager->prepare($query);
        return $pager;
    }

    /**
     * Returns the ID of the last inserted row
     *
     * @return ID of the last row that was inserted into the db
     */
    public function lastInsertId() 
    {
        return $this->handler->lastInsertID();
    }
    
    /**
     * Places quotes around the input string (if required) and escapes 
     * special characters within the input string, using a quoting style 
     * appropriate to the underlying driver. 
     *
     * @param string $string the string to be quoted
     * @param integer $type  data type hint for drivers
     *
     * @return quoted string that is theoretically safe to pass into an SQL statement
     */
    public function quote($string, $type = PDO::PARAM_STR) 
    {
        return $this->handler->quote($string, $type);
    }
    
    public function escape($string, $type = PDO::PARAM_STR) 
    {
        return substr($this->handler->quote($string, $type), 1, -1);
    }

}
