<?php


class variables {

	
	private $variables = array();	// контейнер с переменными
	private $db = null;				// объект-коннектор к базе данных
	private $change = array();		// список измененных во время работы переменных 
	private $delete_ids = array();	// список удаленных объектов
									// (внесение изменений и удаление элементов делается 1 раз - в конце работы скрипта)
	
	private $insert = 1; 
	private $update = 2; 
	private $delete = 3;
	
	
	public function __construct($db) {
	
		$this->db = $db;
		
		// получаем переменные из базы
		$result = $this->db->prepare('SELECT id, name, value FROM variables LIMIT 9999')->execute();
		while ($row = $result->fetch()) {
			$this->variables[$row['name']] = array('id'=>$row['id'], 'value'=>unserialize($row['value']));
		}

	}
	
	
	public function __destruct() {
		$this->save();
	}

	
	// установить переменную
	public function __set($label, $value) {
		if (isset($this->variables[$label])) {
			if ($this->variables[$label]['value']!=$value) { // переменная есть, но она не равна переданной, надо перезаписать
				$this->change[$label] = $this->update; // переменные, которые были переназначены надо обновить
				$this->variables[$label]['value'] = $value;
			}
		} else { // такая переменная не задана, записываем ее в первый раз
			$this->change[$label] = $this->insert;
			$this->variables[$label]['value'] = $value;
		}
	}
	
	
	// установить и сразу записать переменную в базу
	public function set($label, $value) {
		if (isset($this->variables[$label])) {
			if ($this->variables[$label]['value']!=$value) { // переменная есть и она не равна переданной - надо перезаписать
				$this->db->prepare('UPDATE variables SET value=:value WHERE id=:id')
						->bind(':value', serialize($value))
						->bind(':id', $this->variables[$label]['id'])
						->execute();
				$this->variables[$label]['value'] = $value;
			}
		} else { // такая переменная не задана, записываем ее в первый раз
			$this->db->prepare('INSERT INTO variables (name, value) VALUES (:name, :value)')
					->bind(':name', $label)
					->bind(':value', serialize($value))
					->execute();
			$this->variables[$label] = array('id'=>$this->db->lastInsertID(), 'value'=>$value);
		}
	}
	

	// удалить переменную
	public function __unset($label) {
		if (isset($this->variables[$label])) {
			$this->change[$label] = $this->delete; // переменные, которые были удалены, надо удалить и из базы
			$this->delete_ids[$label] = $this->variables[$label]['id']; // id для удаления
			unset($this->variables[$label]);
		}
	}

	
	// возвращаем переменную, если такая есть
	public function __get($label) {
		if (isset($this->variables[$label])) {
			return $this->variables[$label]['value'];
		}
		return FALSE;
	}
	

	// проверка существует ли переменная
	public function __isset($label) {
		return isset($this->variables[$label]);
	}

	
	// запускается при завершении работы скрипта
	private function save() {
		if (!empty($this->change)) { // записываем данные, только если есть изменения
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
						// сделать апдейт сразу на несколько записей нельзя, поэтому обновляем переменные сразу в цикле
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