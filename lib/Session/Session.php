<?php
/**
 * Session class is part of Nanobanano framework
 * wrapper for standart PHP $_SESSION
 *
 * PHP version 5
 *
 * @copyright 2012 Dmitry Elfimov
 * @license   http://www.elfimov.ru/nanobanano/license.txt MIT License
 * @link      http://elfimov.ru/nanobanano
 * 
 */
 
/**
 * Session class
 *
 * @todo  for "remember me" option in login form
 *
 * @package Session
 * @author  Dmitry Elfimov <elfimov@gmail.com>
 *
 */

class Session
{

    /**
     * Constructor.
     */
    public function __construct()
    {
        session_start();
    }
    
    /**
     * Getter.
     *
     * @param string $name name of value in session
     *
     * @return value.
     */
    public function __get($name)
    {
        return $_SESSION[$name];
    }
    
    /**
     * Cookie params setter.
     * see http://ru2.php.net/manual/en/function.session-set-cookie-params.php
     *
     * @param integer $lifetime Lifetime of the session cookie, defined in seconds
     * @param string  $path     Path on the domain where the cookie will work
     * @param string  $domain   Cookie domain
     * @param boolean $secure   If true cookie will only be sent over secure connections
     * @param boolean $httponly If true then PHP will attempt to send the httponly flag
     *
     * @return no value is returned.
     */
    public function setCookieParams($lifetime, $path=null, $domain=null, $secure = null, $httponly = null) {
        session_set_cookie_params($lifetime, $path, $domain, $secure, $httponly);
    }

    /**
     * Setter.
     *
     * @param string $name  name of value in session
     * @param string $value value to set
     *
     * @return no value is returned.
     */
    public function __set($name, $value) 
    {
        $_SESSION[$name] = $value;
    }
    
    /**
     * Check if value is set.
     *
     * @param string $name name of value
     *
     * @return true or false.
     */
    public function __isset($name) 
    {
        return isset($_SESSION[$name]);
    }

    /**
     * Unset session value.
     *
     * @param string $name name of value
     *
     * @return no value is returned.
     */
    public function __unset($name) 
    {
        unset($_SESSION[$name]);
    }
    
    /**
     * Destroy session.
     *
     * @return no value is returned.
     */
    public function destroy() 
    {
        session_destroy();
    }
    
    /**
     * Get session id.
     *
     * @return string session id.
     */
    public function id() 
    {
        return session_id();
    }
    
}