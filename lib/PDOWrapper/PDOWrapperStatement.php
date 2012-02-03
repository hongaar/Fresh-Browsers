<?php

/*
 * PDO wrapper
 * http:/www.elfimov.ru/db
 *
 * Copyright (c) 2011 by Dmitry Elfimov
 * Released under the MIT License.
 * http://www.opensource.org/licenses/mit-license.php
 *
 * Date: 2011-12-12
 * 
 */
 
class PDOWrapperStatement {

	private $handler;
	private $statement;
	
	public function __construct($handler) {
		$this->handler = $handler;
	}
	
	
	public function prepare($query) {
        $this->statement = $this->handler->prepare($query);
		return $this;
	}

    public function bind($pos, $value, $type = null) {

        if (is_null($type) ) {
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

        $this->statement->bindValue($pos, $value, $type);
        return $this;
    }
	

    public function execute(array $parameters=null) {
		$this->statement->execute();
        return $this;
    }
	
	
    public function fetch($fetchStyle=PDO::FETCH_ASSOC) {
        return $this->statement->fetch($fetchStyle);
    }
	
	
    public function fetchObject() {
		return $this->fetch(PDO::FETCH_OBJ);
    }
	

    public function fetchAll($fetchStyle=PDO::FETCH_ASSOC) {
        return $this->statement->fetchAll($fetchStyle);
    }



}