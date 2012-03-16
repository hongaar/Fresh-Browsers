<?php


class session {


	public function __construct() {
		session_start();
	}
	
	public function __get($name) {
		return $_SESSION[$name];
	}

	public function __set($name, $value) {
		$_SESSION[$name] = $value;
	}
	
	public function __isset($name) {
		return isset($_SESSION[$name]);
	}

	public function __unset($name) {
		unset($_SESSION[$name]);
	}
	
	public function destroy() {
		session_destroy();
	}
	
	public function id() {
		return session_id();
	}
	
}