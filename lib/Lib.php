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
 * lib extension with lazy load
 *
 * Example:
 include($__dir.'/lib/lib.php');
 $lib = new lib(
    array(
        'translate' => array('translate'),
        'pdo'       => array('PDO', 'sqlite:'.$__dir.'/versions/browsers.sqlite'),
        'db'        => array('PDOWrapper', '@pdo'),
    ),
    array(
        'translate' => 't', // t() is default method for class translate
    )
 );
 $lib->db->prepare('SELECT * FROM browsers ORDER BY shortName LIMIT 10')
                        ->execute()
                        ->fetchAll();
                        
 echo $lib->translate('string');
 *
 * @package Nanobanano
 * @author  Dmitry Elfimov <elfimov@gmail.com>
 *
 */

class Lib
{

    private $_libCollection = array();
    private $_libs = array();
    private $_defaultMethods = array();
    private $_dir = null;

    /**
     * Constructor.
     *
     * @param array $libs    libs config
     * @param array $default default methods for classes
     */
    public function __construct($libs = null, $default = null)
    {
        $this->_dir = dirname(__FILE__);
        $this->_libs = !empty($libs) ? $libs : $this->_libs;
        $this->_defaultMethods = !empty($default) ? $default : $this->_defaultMethods;
        spl_autoload_register(array($this, 'load'));
    }

    /**
     * Loads class action.
     *
     * @param array $class class name
     *
     * @return none
     */
    public function load($class)
    {
        if (file_exists($this->_dir.'/'.$class.'/'.$class.'.php')) {
            include_once $this->_dir.'/'.$class.'/'.$class.'.php';
        }
    }

    /**
     * Loads class action.
     *
     * @param array $className class name
     *
     * @return class
     */
    private function _getClass($className)
    {
    
        if (isset($this->_libs[$className])) {
            $p = $this->_libs[$className];
            $class = array_shift($p);
        } else {
            $p = array();
            $class = $className;
        }
    
        foreach ($p as &$par) {
            // if parameter name begins with @ - it is a classname
            // 
            if ($par{0} == '@') { 
                $self = substr($par, 1);
                if ($self!=$className) { // anti loop
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
        // code above looks better, but works 50% slower (php 5.3) 
        switch (count($p)) {    
        case 1: 
            $c = new $class($p[0]); 
            break;
        case 2: 
            $c = new $class($p[0], $p[1]); 
            break;
        case 3: 
            $c = new $class($p[0], $p[1], $p[2]); 
            break;
        case 4: 
            $c = new $class($p[0], $p[1], $p[2], $p[3]); 
            break;
        case 5: 
            $c = new $class($p[0], $p[1], $p[2], $p[3], $p[4]); 
            break;
        case 6: 
            $c = new $class($p[0], $p[1], $p[2], $p[3], $p[4], $p[5]); 
            break;
        case 7:
            $c = new $class($p[0], $p[1], $p[2], $p[3], $p[4], $p[5], $p[6]); 
            break;
        case 8: 
            $c = new $class($p[0], $p[1], $p[2], $p[3], $p[4], $p[5], $p[6], $p[7]); 
            break;
        case 9: 
            $c = new $class($p[0], $p[1], $p[2], $p[3], $p[4], $p[5], $p[6], $p[7], $p[8]); 
            break;
        case 10: 
            $c = new $class($p[0], $p[1], $p[2], $p[3], $p[4], $p[5], $p[6], $p[7], $p[8], $p[9]); 
            break;
        case 0: 
        default: 
            $c = new $class(); 
            break;
        }
        return $c;

    }
    
    /**
     * Gets class action.
     *
     * @param array $class class name
     *
     * @return class
     */
    public function __get($class)
    {
        if (!isset($this->_libCollection[$class])) {
            $this->_libCollection[$class] = $this->_getClass($class);
        }
        return $this->_libCollection[$class];
    }
    
    /**
     * Calls method.
     *
     * @param string $name class name
     * @param array  $p    parameters
     *
     * @return result
     */
    public function __call($name, $p)
    {
        $class = $this->__get($name);
        if (isset($this->_defaultMethods[$name])) {
            // return call_user_func_array(array($class, $this->_defaultMethods[$name]), $p);
            // slower then switch .. case
            $method = $this->_defaultMethods[$name];
            switch (count($p)) {
            case 1: 
                $r = $class->$method($p[0]); 
                break;
            case 2: 
                $r = $class->$method($p[0], $p[1]); 
                break;
            case 3: 
                $r = $class->$method($p[0], $p[1], $p[2]); 
                break;
            case 4: 
                $r = $class->$method($p[0], $p[1], $p[2], $p[3]); 
                break;
            case 5: 
                $r = $class->$method($p[0], $p[1], $p[2], $p[3], $p[4]); 
                break;
            case 6: 
                $r = $class->$method($p[0], $p[1], $p[2], $p[3], $p[4], $p[5]); 
                break;
            case 7: 
                $r = $class->$method($p[0], $p[1], $p[2], $p[3], $p[4], $p[5], $p[6]); 
                break;
            case 8: 
                $r = $class->$method($p[0], $p[1], $p[2], $p[3], $p[4], $p[5], $p[6], $p[7]); 
                break;
            case 9: 
                $r = $class->$method($p[0], $p[1], $p[2], $p[3], $p[4], $p[5], $p[6], $p[7], $p[8]); 
                break;
            case 10: 
                $r = $class->$method($p[0], $p[1], $p[2], $p[3], $p[4], $p[5], $p[6], $p[7], $p[8], $p[9]); 
                break;
            case 0:
            default: 
                $r = $class->$method(); 
                break;
            }
            return $r;
        } else if (defined('PHP_VERSION_ID') && PHP_VERSION_ID>=50300) {    
            return call_user_func_array($class, $p); // __invoke for php>5.3
        } else {
            return false;
        }
    }
}