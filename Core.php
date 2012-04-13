<?php
/**
 * Nanobanano framework 
 * 
 * PHP version 5
 *
 * @copyright 2012 Dmitry Elfimov
 * @license   http://www.elfimov.ru/nanobanano/license.txt MIT License
 * @link      http://elfimov.ru/nanobanano
 *
 */
 
/**
 * Core class
 *
 * @package  Nanobanano
 * @author   Dmitry Elfimov <elfimov@gmail.com>
 *
 */
 
class Core
{
    
    public $dir = '';
    public $indexPHP = 'index.php';
    
    public $defaultAction = 'index';
    public $mainTemplate = 'index.tpl.php';
    
    public $requestURI = null;
    
    public $variables = array();
    public $action = '';
    public $rawAction = '';
    public $subDir = '';

    public $host = null;
    public $scheme = null;
    public $port = null;
    
    public $libs = array();
    public $libsDefaultMethods = array();

    /**
     * Constructor.
     */
    public function __construct() 
    {
        $this->dir = dirname(__FILE__);
        $this->requestURI = $_SERVER['REQUEST_URI'];
        
        $ssfLength = strlen($_SERVER['SCRIPT_FILENAME']);
        $sdrLength = strlen($_SERVER['DOCUMENT_ROOT']);
        if ($sdrLength.'/'.$this->indexPHP != $ssfLength) {
            $this->subDir = substr(
                $ssfLength, 
                $sdrLength, 
                $ssfLength - $sdrLength - strlen($this->indexPHP) - 1
            );
            $this->requestURI = substr($this->requestURI, strlen($this->subDir));
        }
        
        $request = ltrim($this->requestURI, "/\/\\ \t\n\r\0\x0B");
        if (($qPos = strpos($request, '?'))!==false) {
            $request = substr($request, 0, $qPos);
        }
        $this->variables = explode('/', $request);
        
        $this->action = $this->getAction($this->variables);
        
        $this->host = $_SERVER['SERVER_NAME'];

        if ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') 
            || (!empty($_SERVER['HTTPS']) && $_SERVER['SERVER_PORT'] == 443)
        ) {
            $this->scheme = 'https';
        } else {
            $this->scheme = 'http';
        }
        
        $this->port = $_SERVER['SERVER_PORT'];
    }

    /**
     * Gets the main action.
     *
     * @param array &$variables exploded request uri
     *
     * @return string
     */
    public function getAction(&$variables = array()) 
    {
        $action = array_shift($variables);
        if (!empty($action)) {
            $this->rawAction = $action;
            $action = preg_replace('/[^a-z0-9_]/', '_', strtolower($action));
        } else {
            $this->rawAction = '';
            $action = $this->defaultAction;
        }
        return $action;
    }
    
    /**
     * Gets library class.
     *
     * @return lib
     */
    public function getLib() 
    {
        if (!isset($this->lib)) {
            include $this->dir.'/lib/Lib.php';
            $this->lib = new Lib($this->libs, $this->libsDefaultMethods);
        }
        return $this->lib;
    }
    
    /**
     * Magic get. Only as wrapper for library class.
     *
     * @param string $name var name
     *
     * @return lib
     */
    public function __get($name) 
    {
        if ($name == 'lib') {
            return $this->getLib();
        }
    }
    
    /**
     * Renders page.
     *
     * @return string
     */
    public function render() 
    {
        if (!file_exists($this->dir.'/tpl/'.$this->action.'.php')) {
            header("HTTP/1.0 404 Not Found");
            header("Status: 404 Not Found");
            $this->action = 'error404';
        }
        $this->out = $this->template($this->action.'.php');
        return $this->template($this->mainTemplate);
    }
    
    /**
     * Simple template.
     *
     * @param string $__template__ template filename (must be in /tpl/ directory)
     * @param array  $__out__      (optional) variables for template
     *
     * @return string
     */
    public function template($__template__, $__out__ = null) 
    {
        if (isset($__out__) && is_array($__out__)) {
            extract($__out__);
        }
        ob_start();
        include $this->dir.'/tpl/'.$__template__;
        return ob_get_clean();
    }
    
    /**
     * Builds link.
     *
     * @param string  $in   base part of link
     * @param boolean $full (optional) will it return the full link or not
     *
     * @return string
     */
    public function link($in, $full=false) 
    {
        $link = '';
        if ($full) {
            $link .= $this->scheme . '://' . $this->host;
            if (($this->port!=80 && $this->scheme=='http') 
                || ($this->port!=443 && $this->scheme=='https')
            ) {
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
