<?php
/*
 * nanobanano framework 
 * https://github.com/Groozly/nanobanano
 *
 * Copyright (c) 2011-2012 by Dmitry Elfimov
 * Released under the MIT License.
 * http://www.opensource.org/licenses/mit-license.php
 *
 * Date: 2012-02-10
 */
 
class core {
	
	public $dir = '';
	public $action = 'index';
	public $mainTemplate = 'index.tpl';
	public $variables = array();
	public $template = '';
	public $lib = null;
	public $libs = array();
	
	
	public function __construct() {
		$this->dir = dirname(__FILE__);
	}
	
	public function render() {
		$this->controller();
		$this->model();
		return $this->view();
	}
	
	public function controller() {
		$request = ltrim($_SERVER['REQUEST_URI'], "/\/\\ \t\n\r\0\x0B");
		$qPos = strpos($request, '?');
		$this->variables = explode('/', substr($request, 0, $qPos===false ? 1024 : $qPos));
		$action = array_shift($this->variables);
		if (isset($action) && $action!='') {
			$this->action = preg_replace('/[^a-zA-Z0-9_]/', '_', $action);
		}
		
	}	
	
	public function model() {
		include($this->dir.'/lib/lib.php');
		$this->lib = new lib($this->libs);
	}	
	
	public function view() {
		$this->template = isset($template) ? $template : $this->action;
		if (!file_exists($this->dir.'/tpl/'.$this->template.'.php')) {
			header("HTTP/1.0 404 Not Found");
			header("Status: 404 Not Found");
			$this->template = 'error404';
		}
		$this->out = $this->template($this->template);
		return $this->template($this->mainTemplate);
	}
	
	public function template($__template__, $__out__ = null) {
		if (isset($__out__) && is_array($__out__)) {
			extract($__out__);
		}
		ob_start();
		require($this->dir.'/tpl/'.$__template__.'.php');
		return ob_get_clean();
	}
	
}