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
 * Date: 2012-03-16
 *
 * Example:
 include($__dir.'/lib/lib.php');
 $lib = new lib(
				array(
					'translate'		=>	array('translate'),
					'pdo'			=>	array('PDO', 'sqlite:'.$__dir.'/versions/browsers.sqlite'),
					'db'			=>	array('PDOWrapper', '@pdo'),
				),
				array(
					'translate'		=>	't', // t() is default method for class translate
				)
			);
 $lib->db->prepare('SELECT * FROM browsers ORDER BY shortName LIMIT 10')
						->execute()
						->fetchAll();
						
 echo $lib->translate('string');
 */

class lib {

	private $libCollection = array();
	private $libs = array();
	private $libsDefaultMethods = array();
	private $dir = null;

	
	public function __construct($libs, $libsDefaultMethods) {
		$this->dir = dirname(__FILE__);
		$this->libs = $libs;
		$this->libsDefaultMethods = $libsDefaultMethods;
	}

	
	private function getClass($className) {
	
		if (isset($this->libs[$className])) {
			$p = $this->libs[$className];
			$class = array_shift($p);
		} else
		if (file_exists($this->dir.'/'.$className.'/'.$className.'.php')) {
			$p = array();
			$class = $className;
		} else {
			return false; // класс не был найден
		}
	
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
	
	
	public function __get($name) {
		if (!isset($this->libCollection[$name])) {
			$this->libCollection[$name] = $this->getClass($name);
		}
		return $this->libCollection[$name];
	}
	
	
	public function __call($name, $p) {
		$class = $this->__get($name);
		if (isset($this->libsDefaultMethods[$name])) {
			// this is slower then switch .. case
			// return call_user_func_array(array($class, $this->libsDefaultMethods[$name]), $p);
			$method = $this->libsDefaultMethods[$name];
			switch (count($p)) {
				case 1: $r = $class->$method($p[0]); break;
				case 2: $r = $class->$method($p[0], $p[1]); break;
				case 3: $r = $class->$method($p[0], $p[1], $p[2]); break;
				case 4: $r = $class->$method($p[0], $p[1], $p[2], $p[3]); break;
				case 5: $r = $class->$method($p[0], $p[1], $p[2], $p[3], $p[4]); break;
				case 6: $r = $class->$method($p[0], $p[1], $p[2], $p[3], $p[4], $p[5]); break;
				case 7: $r = $class->$method($p[0], $p[1], $p[2], $p[3], $p[4], $p[5], $p[6]); break;
				case 8: $r = $class->$method($p[0], $p[1], $p[2], $p[3], $p[4], $p[5], $p[6], $p[7]); break;
				case 9: $r = $class->$method($p[0], $p[1], $p[2], $p[3], $p[4], $p[5], $p[6], $p[7], $p[8]); break;
				case 10: $r = $class->$method($p[0], $p[1], $p[2], $p[3], $p[4], $p[5], $p[6], $p[7], $p[8], $p[9]); break;
				case 0:
				default: $r = $class->$method(); break;
			}
			return $r;
		} else 
		if (defined('PHP_VERSION_ID') && PHP_VERSION_ID>=50300) {	// __invoke for php>5.3
			return call_user_func_array($class, $p);
		} else {
			return false;
		}
	}
}