<?php
/**
 * User class is part of Nanobanano framework
 *
 * PHP version 5
 *
 * @copyright 2012 Dmitry Elfimov
 * @license   http://www.elfimov.ru/nanobanano/license.txt MIT License
 * @link      http://elfimov.ru/nanobanano
 * 
 */

/*
 * User class
 *
 * @package Session
 * @author  Dmitry Elfimov <elfimov@gmail.com>
 *
 * Examples:

// get field 'name' or return false if not set
echo $this->lib->user->name;

// user id
echo $this->lib->user->id;

// login
$this->lib->user->login('test', 'test');

// logout
$this->lib->user->logout();

// adds user and returns id of new user or false if fails to add. 
// must be logged user with role 'edit_users' or 'admin'
$id = $this->lib->user->add('test', 'test', 'test@test.com'); 

// load user 
$user = $this->lib->user->load($id);

// save changed user. must have role 'edit_users' or 'admin'
$this->lib->user->save($user);

 */

class User
{
    
    private $_db = null;
    
    private $_session = null;
    
    private $_user = false;
    
    private $_userStorage = array();
    
    private $_salt = 'salt12345';
    
    private $_roles = array(
        'admin'    =>    array('edit_users')
    );

    /**
     * Constructor.
     *
     * @param PDOWrapper $db      database handler
     * @param Session    $session session class
     */
    public function __construct(PDOWrapper $db, Session $session)
    {
        $this->_db = $db;
        $this->_session = $session;
        $this->auth();
        register_shutdown_function(array($this, 'save'));
    }
    
    /**
     * Adds new user.
     *
     * @param string  $login    users login
     * @param string  $password password
     * @param string  $email    email
     * @param integer $active   0 or 1
     * @param array   $data     additional data array
     *
     * @return added user id or false on error.
     */
    public function add($login, $password, $email='', $active=1, $data=array())
    {
        if (!in_array('edit_users', $this->_user->roles)) {
            return false;
        }
        $result = $this->_db->prepare(
            'INSERT INTO users'
            .' (login, password, email, active, data, created, modified)'
            .' VALUES'
            .' (:login, :password, :email, :active, :data, :created, :modified)'
        )
            ->bind(':login', $login)
            ->bind(':password', $this->_hash($password))
            ->bind(':email', $email)
            ->bind(':active', $active, PDO::PARAM_INT)
            ->bind(':data', empty($data) ? '' : serialize($data))
            ->bind(':created', time())
            ->bind(':modified', time())
            ->execute();
        if ($result===false) {
            return false;
        } else {
            return $this->_db->lastInsertId();
        }
    }
    
    /**
     * Loads user with specified id.
     *
     * @param integer $id users id
     *
     * @return user object or false
     */
    public function load($id)
    {
        if (!isset($this->_userStorage[$id])) {
            $result = $this->_db->prepare('SELECT * FROM users WHERE id = ? LIMIT 1')
                ->bind(1, $id, PDO::PARAM_INT);
            $this->_userStorage[$user['id']] = $this->_loadUser($result);
        }
        return $this->_userStorage[$id];
    }
    
    /**
     * Loads user with prepared db context.
     *
     * @param PDOWrapper $prepare db handler
     *
     * @return user object or false
     */
    private function _loadUser($prepare) 
    {
        $user = $prepare->execute()->fetch();
        if ($user!==false) {
            $user['data'] = empty($user['data']) ? array() : unserialize($user['data']);
            if (empty($user['roles'])) {
                $user['roles'] = array();
            } else {
                $user['roles'] = unserialize($user['roles']);
                $roles = $user['roles'];
                foreach ($roles as $role) {
                    if (isset($this->_roles[$role])) {
                        $user['roles'] += $this->_roles[$role];
                    }
                }
            }
        }
        return $user;
    }

    /**
     * Authenticate user if userID in session is set.
     *
     * @return user object or false
     */
    public function auth() 
    {
        if ($this->_user===false && !empty($this->_session->userID)) {
            $this->_user = $this->load($this->_session->userID);
        } 
        return $this->_user;
    }
    
    
    /**
     * Get user or user property if $name is specified.
     *
     * @param string $name property name
     *
     * @return user object, user property value or false
     */
    public function get($name=null) 
    {
        if (empty($name)) {
            $return = $this->_user;
        } else if ($this->_user!==false) {
            if (isset($this->_user[$name])) {
                $return = $this->_user[$name];
            } else if (isset($this->_user['data'][$name])) {
                $return = $this->_user['data'][$name];
            } else {
                $return = false;
            }
        }
        return $return;
    }
    
    /**
     * Get user property
     *
     * @param string $name property name
     *
     * @return user user property value or false
     */
    public function __get($name) 
    {
        if ($this->_user===false) {
            return false;
        }
        if (isset($this->_user[$name])) {
            return $this->_user[$name];
        } else if (isset($this->_user['data'][$name])) {
            return $this->_user['data'][$name];
        } else {
            return false;
        }
    }
    
    /**
     * Set user property
     *
     * @param string $name  property name
     * @param string $value property value
     *
     * @return no value is returned.
     */
    public function __set($name, $value) 
    {
        $this->_user['data'][$name] = $value;
    }
    
    /**
     * Authenticate user with specified login and password.
     *
     * @param string $login    users email or login
     * @param string $password users password
     *
     * @return user object or false
     */
    public function login($login, $password) 
    {
        $prepare = $this->_db->prepare(
            'SELECT * FROM users'
            .' WHERE'
            .' (login=:login OR email=:login)'
            .' AND password=:password'
            .'AND active=1'
            .' LIMIT 1'
        )
            ->bind(':login', $login)
            ->bind(':password', $this->_hash($password));
        $this->_user = $this->_loadUser($prepare);
        if ($this->_user!==false) {
            $this->_session->userID = $this->_user['id'];
        }
        return $this->_user;
    }
    
    /**
     * Logout.
     *
     * @return no value is returned.
     */
    public function logout() 
    {
        unset($this->_session->userID);
        $this->_user = false;
    }

    /**
     * Hash.
     *
     * @param string $password password to hash
     *
     * @return string hash value.
     */
    private function _hash($password) 
    {
        return substr(crypt($password, '$5$rounds=5000$'.$this->salt.'$'), 32);
        // return md5($password . $this->salt);
    }
    
    
    /**
     * Save modified users properties.
     *
     * @param string $user user object to save
     *
     * @return PDOWrapperStatement or false.
     */
    public function save($user=null)
    {
        $params = array('data', 'roles');
        if (!isset($user)) {
            $user = $this->_user;
        } else if (in_array('edit_users', $this->_user->roles)) {
            $params = array_keys($user);
        }
        
        if (!isset($this->userStorage[$user['id']])) {
            return false;
        }
        
        $changed = array();
        $changedParams = array();
        
        foreach ($params as $param) {
            if (isset($this->userStorage[$user['id']][$param]) 
                && $this->userStorage[$user['id']][$param]!=$user[$param]
            ) {
                $changed[] = $param;
                $changedParams[] = $param.'=?';
            }
        }
        
        if (!empty($changed)) {
            $changedParams[] = 'modified=?';
            $result = $this->_db->prepare(
                'UPDATE users SET '
                .implode(', ', $changedParams).' WHERE id=?'
            );
            $i = 1;
            foreach ($changed as $param) {
                $value = $user[$param];
                if (($param=='data' || $param=='roles') && !empty($value)) {
                    $value = serialize($value);
                }
                $result->bind($i, $value);
                $i++;
            }
            return $result->bind($i, time())->bind($i+1, $user['id'], PDO::PARAM_INT)
                ->execute();
        }
        
        return false;
    }

}