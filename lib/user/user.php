<?php


/*
 * nanobanano framework 
 * user class
 *
 * https://github.com/Groozly/nanobanano
 *
 * Copyright (c) 2011 by Dmitry Elfimov
 * Released under the MIT License.
 * http://www.opensource.org/licenses/mit-license.php
 *
 * Date: 2012-03-16
 *
 * Examples:

 echo $this->lib->user->name;	//get field 'name' or return false if not set
 echo $this->lib->user->id;		// user id
 
 $this->lib->user->login('test', 'test');	// login
 
 $this->lib->user->logout(); 	// logout
 
 $id = $this->lib->user->add('test', 'test', 'test@test.com'); // add user and return new user id or false if failed to add. must be logged user with role 'edit_users' (or 'admin')
 
 $user = $this->lib->user->load($id); 	// load user 
 $this->lib->user->save($user);			// save changed user. must have role 'edit_users' or 'admin'
	
 */

class user {
	
	private $db = null;
	
	private $user = false;
	
	private $userStorage = array();
	
	private $salt = 'salt12345';
	
	private $session = null;
	
	private $roles = array(
		'admin'	=>	array('edit_users')
	);

    public function __construct(PDOWrapper $db) {
		$this->db = $db;
		include_once(dirname(__FILE__).'/session.php');
		$this->session = new session();
		$this->auth();
    }
	
	// save user profile
	public function __destruct() {
		$this->save();
	}
	
	
	public function add($login, $password, $email='', $active=1, $data=array()) {
		if (!in_array('edit_users', $this->user->roles)) {
			return false;
		}
		$result = $this->db->prepare('INSERT INTO users (login, password, email, active, data, created, modified) VALUES (:login, :password, :email, :active, :data, :created, :modified)')
					->bind(':login', $login)
					->bind(':password', $this->hash($password))
					->bind(':email', $email)
					->bind(':active', $active, PDO::PARAM_INT)
					->bind(':data', empty($data) ? '' : serialize($data))
					->bind(':created', time())
					->bind(':modified', time())
					->execute();
		if ($result===false) {
			return false;
		} else {
			return $this->db->lastInsertId();
		}
	}
	
	
	public function load($id) {
		
		if (isset($this->userStorage[$id])) {
			return $this->userStorage[$id];
		}
		
		$result = $this->db->prepare('SELECT * FROM users WHERE id = ? LIMIT 1')
						->bind(1, $id, PDO::PARAM_INT);
		return $this->loadUser($result);
	}
	
	
	private function loadUser($prepare) {
		$user = $prepare->execute()->fetch();
		if ($user!==false) {
			$user['data'] = empty($user['data']) ? array() : unserialize($user['data']);
			if (empty($user['roles'])) {
				$user['roles'] = array();
			} else {
				$user['roles'] = unserialize($user['roles']);
				$roles = $user['roles'];
				foreach ($roles as $role) {
					if (isset($this->roles[$role])) {
						$user['roles'] += $this->roles[$role];
					}
				}
			}
			$this->userStorage[$user['id']] = $user;
		}
		return $user;
	}

	
	public function auth() {
		if ($this->user===false && isset($this->session->userID)) {
			$this->user = $this->load($this->session->userID);
		} 
		return $this->user;
	}
	
	public function get($name=null) {
		if ($this->user!==false && isset($name)) {
			if (isset($this->user[$name])) {
				return $this->user[$name];
			} else
			if (isset($this->user['data'][$name])) {
				return $this->user['data'][$name];
			} else {
				return false;
			}
		}
		return $this->user;
	}
	
	public function __get($name) {
		if ($this->user===false) return false;
		if (isset($this->user[$name])) {
			return $this->user[$name];
		} else
		if (isset($this->user['data'][$name])) {
			return $this->user['data'][$name];
		} else {
			return false;
		}
	}
	
	public function __set($name, $value) {
		$this->user['data'][$name] = $value;
	}
	
	
	public function login($login, $password) {
		$prepare = $this->db->prepare('SELECT * FROM users WHERE (login=:login OR email=:login) AND password=:password AND active=1 LIMIT 1')
				->bind(':login', $login)
				->bind(':password', $this->hash($password));
		$this->user = $this->loadUser($prepare);
		if ($this->user!==false) {
			$this->session->userID = $this->user['id'];
		}
		return $this->user;
	}
	
	public function logout() {
		unset($this->session->userID);
	}

	private function hash($password) {
		return substr(crypt($password, '$5$rounds=5000$'.$this->salt.'$'), 32);
		// return md5($password . $this->salt);
	}
	
	
	
	public function save($user=null) {
	
		$params = array('data', 'roles');
		if (!isset($user)) {
			$user = $this->user;
		} else 
		if (in_array('edit_users', $this->user->roles)) {
			$params = array_keys($user);
		}
		
		if (!isset($this->userStorage[$user['id']])) {
			return false;
		}
		
		$changed = array();
		$changedParams = array();
		
		foreach ($params as $param) {
			if (isset($this->userStorage[$user['id']][$param]) 
				&& $this->userStorage[$user['id']][$param]!=$user[$param]) {
				$changed[] = $param;
				$changedParams[] = $param.'=?';
			}
		}
		
		if (!empty($changed)) {
			$changedParams[] = 'modified=?';
			$result = $this->db->prepare('UPDATE users SET '.implode(', ', $changedParams).' WHERE id=?');
			$i = 1;
			foreach ($changed as $param) {
				$value = $user[$param];
				if (($param=='data' || $param=='roles') && !empty($value)) {
					$value = serialize($value);
				}
				$result->bind($i, $value);
				$i++;
			}
			return $result->bind($i, time())->bind($i+1, $user['id'], PDO::PARAM_INT)->execute();
		}
		
		return false;
	}
	

}