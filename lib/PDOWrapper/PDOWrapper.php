<?php

/*
 * PDO wrapper
 * http:/www.elfimov.ru/pdo
 *
 * Copyright (c) 2011 by Dmitry Elfimov
 * Released under the MIT License.
 * http://www.opensource.org/licenses/mit-license.php
 *
 * Date: 2011-12-12
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


class PDOWrapper {

    public $handler;
	
    public function __construct($pdo) {
        $this->handler = $pdo;
		include_once(__DIR__.'/PDOWrapperStatement.php');
    }
	

    public function prepare($query) {
		$stmt = new PDOWrapperStatement($this->handler);
		$stmt->prepare($query);
        return $stmt;
    }
	

	public function lastInsertID() {
		return $this->handler->lastInsertID();
	}

}
