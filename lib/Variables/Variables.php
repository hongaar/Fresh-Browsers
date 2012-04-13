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
    // ��������� � �����������
    private $_variables = array();  
    
    // ������-��������� � ���� ������
    private $_db = null;
    
    // ������ ���������� �� ����� ������ ���������� 
    private $_modified = array();
    
    // ������ ��������� �������� 
    // (�������� ��������� � �������� ��������� �������� 1 ��� - � ����� ������ �������)
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
        // �������� ���������� �� ����
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
            // ���������� ����, �� ��� �� ����� ����������, ���� ������������
            if ($this->_variables[$name]['value']!=$value) {
                // ����������, ������� ���� ������������� ���� ��������
                $this->_modified[$name] = self::UPDATE;
                $this->_variables[$name]['value'] = $value;
            }
        } else { // ����� ���������� �� ������, ���������� �� � ������ ���
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
            // ���������� ���� � ��� �� ����� ���������� - ���� ������������
            if ($this->_variables[$name]['value']!=$value) {
                $this->_db->prepare('UPDATE variables SET value=:value WHERE id=:id')
                    ->bind(':value', serialize($value))
                    ->bind(':id', $this->_variables[$name]['id'])
                    ->execute();
                $this->_variables[$name]['value'] = $value;
            }
        } else { // ����� ���������� �� ������, ���������� �� � ������ ���
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
            // ����������, ������� ���� �������, ���� ������� �� ����
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
        if (!empty($this->_modified)) { // ���������� ������, ������ ���� ���� ���������
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
                    // ������� ������ ����� �� ��������� ������� ������, 
                    // ������� ��������� ���������� � �����
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