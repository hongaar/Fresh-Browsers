<?php

/*
 * nanobanano framework 
 * lib extension with lazy load. 
 *
 * https://github.com/Groozly/nanobanano
 *
 * Copyright (c) 2011 by Dmitry Elfimov
 * Released under the MIT License.
 * http://www.opensource.org/licenses/mit-license.php
 *
 * Date: 2012-02-07
 *
 * Example:
 include($__dir.'/lib/lib.php');
 $lib = new lib(array(
				't'		=>	array('translate'),
				'pdo'	=>	array('PDO', 'sqlite:'.$__dir.'/versions/browsers.sqlite'),
				'db'	=>	array('PDOWrapper', '@pdo'),
			));
 $lib->db->prepare('SELECT * FROM browsers ORDER BY shortName LIMIT 10')
						->execute()
						->fetchAll();
	
 */

class lib {

	private $libCollection = array();
	private $libs = array();
	private $dir = null;

	
	public function __construct($libs) {
		$this->dir = dirname(__FILE__);
		$this->libs = $libs;
	}

	
	private function getClass($className) {
		foreach ($this->libs as $name=>$p) {
			if ($className==$name) {
				$class = array_shift($p);
				if (!class_exists($class, false)) { // не встроенный класс
					include_once($this->dir.'/'.$class.'/'.$class.'.php');
				}
				foreach ($p as &$par) {
					if ($par{0} == '@') { // имя параметра начинается с @ - значит это какой-то класс, описанный при вызове конструктора
						$self = substr($par, 1);
						if ($self!=$className) { // antiloop
							$newPar = $this->__get($self);
							if ($newPar!==false) {
								$par = $newPar;
							}
						}
					}
				}
				
				/*
				$reflectionClass = new \ReflectionClass($class);
				return $reflectionClass->newInstanceArgs($p);
				*/
				// код выше красивее, меньше и не имеет ограничения на 10 параметров, но работает примерно на 50% медленней (php 5.3) 
				switch (count($p)) {	
					case 0: $c = new $class(); break;
					case 1: $c = new $class($p[0]); break;
					case 2: $c = new $class($p[0], $p[1]); break;
					case 3: $c = new $class($p[0], $p[1], $p[2]); break;
					case 4: $c = new $class($p[0], $p[1], $p[2], $p[3]); break;
					case 5: $c = new $class($p[0], $p[1], $p[2], $p[3], $p[4]); break;
					case 6: $c = new $class($p[0], $p[1], $p[2], $p[3], $p[4], $p[5]); break;
					case 7: $c = new $class($p[0], $p[1], $p[2], $p[3], $p[4], $p[5], $p[6]); break;
					case 8: $c = new $class($p[0], $p[1], $p[2], $p[3], $p[4], $p[5], $p[6], $p[7]); break;
					case 9: $c = new $class($p[0], $p[1], $p[2], $p[3], $p[4], $p[5], $p[6], $p[7], $p[8]); break;
					case 10: $c = new $class($p[0], $p[1], $p[2], $p[3], $p[4], $p[5], $p[6], $p[7], $p[8], $p[9]); break;
					default: $c = new $class(); break;
				}
				return $c;
			}
		}
		return false; // класс не был найден
	}
	
	
	public function __get($name) {
		if (!isset($this->libCollection[$name])) {
			$this->libCollection[$name] = $this->getClass($name);
		}
		return $this->libCollection[$name];
	}
}