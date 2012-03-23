<?php


class variables {

	
	private $variables = array();	// ��������� � �����������
	private $db = null;				// ������-��������� � ���� ������
	private $change = array();		// ������ ���������� �� ����� ������ ���������� 
	private $delete_ids = array();	// ������ ��������� ��������
									// (�������� ��������� � �������� ��������� �������� 1 ��� - � ����� ������ �������)
	
	private $insert = 1; 
	private $update = 2; 
	private $delete = 3;
	
	
	public function __construct($db) {
	
		$this->db = $db;
		
		// �������� ���������� �� ����
		$result = $this->db->prepare('SELECT id, name, value FROM variables LIMIT 9999')->execute();
		while ($row = $result->fetch()) {
			$this->variables[$row['name']] = array('id'=>$row['id'], 'value'=>unserialize($row['value']));
		}

	}
	
	
	public function __destruct() {
		$this->save();
	}

	
	// ���������� ����������
	public function __set($label, $value) {
		if (isset($this->variables[$label])) {
			if ($this->variables[$label]['value']!=$value) { // ���������� ����, �� ��� �� ����� ����������, ���� ������������
				$this->change[$label] = $this->update; // ����������, ������� ���� ������������� ���� ��������
				$this->variables[$label]['value'] = $value;
			}
		} else { // ����� ���������� �� ������, ���������� �� � ������ ���
			$this->change[$label] = $this->insert;
			$this->variables[$label]['value'] = $value;
		}
	}
	
	
	// ���������� � ����� �������� ���������� � ����
	public function set($label, $value) {
		if (isset($this->variables[$label])) {
			if ($this->variables[$label]['value']!=$value) { // ���������� ���� � ��� �� ����� ���������� - ���� ������������
				$this->db->prepare('UPDATE variables SET value=:value WHERE id=:id')
						->bind(':value', serialize($value))
						->bind(':id', $this->variables[$label]['id'])
						->execute();
				$this->variables[$label]['value'] = $value;
			}
		} else { // ����� ���������� �� ������, ���������� �� � ������ ���
			$this->db->prepare('INSERT INTO variables (name, value) VALUES (:name, :value)')
					->bind(':name', $label)
					->bind(':value', serialize($value))
					->execute();
			$this->variables[$label] = array('id'=>$this->db->lastInsertID(), 'value'=>$value);
		}
	}
	

	// ������� ����������
	public function __unset($label) {
		if (isset($this->variables[$label])) {
			$this->change[$label] = $this->delete; // ����������, ������� ���� �������, ���� ������� � �� ����
			$this->delete_ids[$label] = $this->variables[$label]['id']; // id ��� ��������
			unset($this->variables[$label]);
		}
	}

	
	// ���������� ����������, ���� ����� ����
	public function __get($label) {
		if (isset($this->variables[$label])) {
			return $this->variables[$label]['value'];
		}
		return FALSE;
	}
	

	// �������� ���������� �� ����������
	public function __isset($label) {
		return isset($this->variables[$label]);
	}

	
	// ����������� ��� ���������� ������ �������
	private function save() {
		if (!empty($this->change)) { // ���������� ������, ������ ���� ���� ���������
			$deleteIds = array();
			$insertArr = array();
			foreach ($this->change as $name => $action) {
				switch ($action) {
					case $this->delete:
						$deleteIds[] = $this->delete_ids[$name];
						break;
					case $this->insert:
						$insertArr[] = array('name' => $name, 'value' => serialize($this->variables[$name]['value']));
						break;
					case $this->update:
						// ������� ������ ����� �� ��������� ������� ������, ������� ��������� ���������� ����� � �����
						$this->db->prepare('UPDATE variables SET value=:value WHERE id=:id')
								->bind(':value', serialize($this->variables[$name]['value']))
								->bind(':id', $this->variables[$name]['id'])
								->execute();
						break;
					default:
						break;					
				}
			}
			if (!empty($deleteIds)) {
				$this->db->prepare('DELETE FROM variables WHERE id IN ('.implode(',',$deleteIds).')')
						->execute();
			}
			
			if (!empty($insertArr)) {
				$handler = $this->db->prepare('INSERT INTO variables (name, value) VALUES (:name, :value)');
				foreach ($insertArr as $n=>$arr) {
					$handler->bind(':name', $arr['name'])
							->bind(':value', $arr['value'])
							->execute();
				}
				
			}
		}
	}


}