<?php
/**
 * Variables is part of Nanobanano framework
 *
 * PHP version 5
 *
 * @copyright 2012 Dmitry Elfimov
 * @license   http://www.elfimov.ru/nanobanano/license.txt MIT License
 * @link      http://elfimov.ru/nanobanano
 * 
 */
 
/*
 * Variables class
 *
 * @package Session
 * @author  Dmitry Elfimov <elfimov@gmail.com>
 *
 */

class Variables
{
    // контейнер с переменными
    private $_variables = array();  
    
    // объект-коннектор к базе данных
    private $_db = null;
    
    // список измененных во время работы переменных 
    private $_modified = array();
    
    // список удаленных объектов 
    // (внесение изменений и удаление элементов делается 1 раз - в конце работы скрипта)
    private $_deletedIds = array(); 
    
    const INSERT = 1; 
    const UPDATE = 2; 
    const DELETE = 3;
    
    /**
     * Constructor.
     *
     * @param PDOWrapper $db database handler
     */
    public function __construct($db) 
    {
        $this->_db = $db;
        // получаем переменные из базы
        $result = $this->_db->prepare('SELECT id, name, value FROM variables LIMIT 9999')
            ->execute();
        while ($row = $result->fetch()) {
            $this->_variables[$row['name']] = array(
                'id'=>$row['id'], 
                'value'=>unserialize($row['value'])
            );
        }
        register_shutdown_function(array($this, 'save'));
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
        if (isset($this->_variables[$name])) {
            // переменная есть, но она не равна переданной, надо перезаписать
            if ($this->_variables[$name]['value']!=$value) {
                // переменные, которые были переназначены надо обновить
                $this->_modified[$name] = self::UPDATE;
                $this->_variables[$name]['value'] = $value;
            }
        } else { // такая переменная не задана, записываем ее в первый раз
            $this->_modified[$name] = self::INSERT;
            $this->_variables[$name]['value'] = $value;
        }
    }
    
    
    /**
     * Set variable and write it to db immediately
     *
     * @param string $name  name of value in session
     * @param string $value value to set
     *
     * @return no value is returned.
     */
    public function set($name, $value)
    {
        if (isset($this->_variables[$name])) {
            // переменная есть и она не равна переданной - надо перезаписать
            if ($this->_variables[$name]['value']!=$value) {
                $this->_db->prepare('UPDATE variables SET value=:value WHERE id=:id')
                    ->bind(':value', serialize($value))
                    ->bind(':id', $this->_variables[$name]['id'])
                    ->execute();
                $this->_variables[$name]['value'] = $value;
            }
        } else { // такая переменная не задана, записываем ее в первый раз
            $this->_db
                ->prepare('INSERT INTO variables (name, value) VALUES (:name, :value)')
                ->bind(':name', $name)
                ->bind(':value', serialize($value))
                ->execute();
            $this->_variables[$name] = array(
                'id'=>$this->_db->lastInsertID(), 
                'value'=>$value
            );
        }
    }

    /**
     * Unset variable
     *
     * @param string $name name of value
     *
     * @return no value is returned.
     */
    public function __unset($name)
    {
        if (isset($this->_variables[$name])) {
            // переменные, которые были удалены, надо удалить из базы
            $this->_modified[$name] = self::DELETE;
            $this->_deletedIds[$name] = $this->_variables[$name]['id']; 
            unset($this->_variables[$name]);
        }
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
        if (isset($this->_variables[$name])) {
            return $this->_variables[$name]['value'];
        }
        return false;
    }
    

    /**
     * Check if variable is set.
     *
     * @param string $name name of value
     *
     * @return true or false.
     */
    public function __isset($name)
    {
        return isset($this->_variables[$name]);
    }

    
    /**
     * Save modified variable.
     *
     * @return no value is returned.
     */
    public function save()
    {
        if (!empty($this->_modified)) { // записываем данные, только если есть изменения
            $deleteIds = array();
            $insertArr = array();
            foreach ($this->_modified as $name => $action) {
                switch ($action) {
                case self::DELETE:
                    $deleteIds[] = $this->_deletedIds[$name];
                    break;
                case self::INSERT:
                    $insertArr[] = array(
                        'name' => $name, 
                        'value' => serialize($this->_variables[$name]['value'])
                    );
                    break;
                case self::UPDATE:
                    // сделать апдейт сразу на несколько записей нельзя, 
                    // поэтому обновляем переменные в цикле
                    $this->_db->prepare('UPDATE variables SET value=:value WHERE id=:id')
                        ->bind(':value', serialize($this->_variables[$name]['value']))
                        ->bind(':id', $this->_variables[$name]['id'])
                        ->execute();
                    break;
                default:
                    break;                    
                }
            }

            if (!empty($deleteIds)) {
                $this->_db->prepare('DELETE FROM variables WHERE id IN ('.implode(',', $deleteIds).')')
                    ->execute();
            }
            
            if (!empty($insertArr)) {
                $handler = $this->_db->prepare('INSERT INTO variables (name, value) VALUES (:name, :value)');
                foreach ($insertArr as $n=>$arr) {
                    $handler->bind(':name', $arr['name'])->bind(':value', $arr['value'])
                        ->execute();
                }
            }
        }
    }

}