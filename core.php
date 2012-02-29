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
	public $indexPHP = 'index.php';
	public $action = 'index';
	public $mainTemplate = 'index.tpl';
	public $subDir = '';
	public $variables = array();
	public $template = '';
	public $lib = null;
	public $libs = array();
	
	public $requestURI = null;
	
	
	public function __construct() {
		$this->dir = dirname(__FILE__);
	}
	
	public function render() {
		$this->controller();
//		print_r($_SERVER);
//		print_r($this->action);
//		print_r($this->variables);
		$this->model();
		return $this->view();
	}
	
	public function controller() {
		if (isset($_SERVER['DOCUMENT_ROOT']) && isset($_SERVER['SCRIPT_FILENAME']) && ($_SERVER['DOCUMENT_ROOT'].'/'.$this->indexPHP!=$_SERVER['SCRIPT_FILENAME'])) {
			$serverScriptFilenameLength = strlen($_SERVER['SCRIPT_FILENAME']);
			$serverDocumentRootLength = strlen($_SERVER['DOCUMENT_ROOT']);
			$this->subDir = substr($_SERVER['SCRIPT_FILENAME'], $serverDocumentRootLength, $serverScriptFilenameLength-$serverDocumentRootLength-strlen($this->indexPHP)-1);
		}
		$this->requestURI = ($this->subDir=='') ? $_SERVER['REQUEST_URI'] : substr($_SERVER['REQUEST_URI'], strlen($this->subDir));
		$request = ltrim($this->requestURI, "/\/\\ \t\n\r\0\x0B");
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
		if (!file_exists($this->dir.'/tpl/'.$this->action.'.php')) {
			header("HTTP/1.0 404 Not Found");
			header("Status: 404 Not Found");
			$this->action = 'error404';
		}
		$this->out = $this->template($this->action);
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
	
	public function link ($link) {
		return $this->subDir.((isset($link{0}) && $link{0}!='/')?'/':'').$link;
	}
	
}