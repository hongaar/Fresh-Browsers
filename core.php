<?php
/*
 * nanobanano framework 
 * https://github.com/Groozly/nanobanano
 *
 * Copyright (c) 2011-2012 by Dmitry Elfimov
 * Released under the MIT License.
 * http://www.opensource.org/licenses/mit-license.php
 *
 * Date: 2012-03-23
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
	public $libsDefaultMethods = array();
	
	public $host = null;
	public $scheme = null;
	public $port = null;
	
	public $requestURI = null;
	
	
	public function __construct() {
		$this->dir = dirname(__FILE__);
	}
	
	public function render() {
		$this->controller();
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
		
		if (!empty($action)) {
			$actionOption = strtolower($action);
			$options = array();
			foreach ($this->options as $key=>$values) {
				if (in_array($actionOption, $values)) {
					$options[$key] = $actionOption;
					$action = array_shift($this->variables);
					break;
				}
			}
			$this->options = $options;
		}
		
		if (!empty($action)) {
			$this->action = preg_replace('/[^a-zA-Z0-9_]/', '_', $action);
		}
		
		$this->host = $_SERVER['SERVER_NAME'];

		if ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || (!empty($_SERVER['HTTPS']) && $_SERVER['SERVER_PORT'] == 443)) {
			$this->scheme = 'https';
		} else {
			$this->scheme = 'http';
		}
		
		$this->port = $_SERVER['SERVER_PORT'];
		
	}

	
	public function model() {
		include($this->dir.'/lib/lib.php');
		$this->lib = new lib($this->libs, $this->libsDefaultMethods);
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
	
	public function link ($in, $full=false) {
		$link = '';
		if ($full) {
			$link .= $this->scheme . '://' . $this->host;
			if (($this->port!=80 && $this->scheme=='http') || ($this->port!=443 && $this->scheme=='https')) {
				$link .= ':' . $this->port;
			}
		}
		$link .= $this->subDir;
		if (isset($in{0}) && $in{0} != '/') {
			$link .= '/';
		}
		$link .= $in;
		return $link;
	}
	
}