<?php

$this->edit = true;

if (!$this->edit) {
	return false;
}

$browsersArr = $this->lib->db->prepare('SELECT * FROM browsers ORDER BY shortName LIMIT 20')
						->execute()
						->fetchAll();
					
$browsers = array();
$browsersValues = array(-1=>'---');

foreach ($browsersArr as $browser) {
	$shortName = strtolower($browser['shortName']);
	$browsers[$shortName] = $browser;
	$browsersValues[$browser['id']] = $browser['name'];
}

$branches = $this->lib->browsersVersions->branches;
$branches[-1] = '---';

$values = array(
	'browser'	=>	-1,
	'branch'	=>	-1,
	'version'	=>	'',
	'date'		=>	'',
	'note'		=>	'',
);
$releaseTime = 0;

$historyId = 0;
if (isset($this->variables[0])) {
	$id = (int) $this->variables[0];
	$historyObj = $this->lib->db->prepare('SELECT * FROM history WHERE id=:id')
						->bind(':id', $id)
						->execute()
						->fetch();
	if ($historyObj!==false) {
		$values = array(
			'browser'	=>	$historyObj['browserId'],
			'branch'	=>	$historyObj['branch'],
			'version'	=>	$historyObj['releaseVersion'],
			'date'		=>	date('Y-m-d', $historyObj['releaseDate']),
			'note'		=>	$historyObj['note'],
		);
		$releaseTime = $historyObj['releaseDate'];
		$historyId = $id;
	}
}



if (isset($_POST) && !empty($_POST)) {
	foreach ($values as $id=>&$value) {
		if (isset($_POST[$id])) {
			$newValue = $_POST[$id];
			switch (true) {
				case $id=='browser' && isset($browsers[$newValue]):
					$value = $newValue;
					break;
					
				case $id=='branch' && isset($branches[$newValue]):
					$value = $newValue;
					break;
				
				case $id=='date':
					$releaseTime = strtotime($newValue);
					if ($releaseTime>0) {
						$value = date('Y-m-d', $releaseTime);
					}					
					break;
					
				default:
					$value = html_entity_decode($newValue, null, 'UTF-8');
			}
		}
	}
	
	
	if ($values['browser']!=-1 && $values['branch']!=-1) {
		if ($historyId>0) {
			$prepare = $this->lib->db->prepare('UPDATE history SET browserId=:browserId, branch=:branch, releaseVersion=:releaseVersion, releaseDate=:releaseDate, __modified=:modified WHERE id=:id');
		} else {
			$prepare = $this->lib->db->prepare('INSERT INTO history (browserId, branch, releaseVersion, releaseDate, __modified) VALUES (:browserId, :branch, :releaseVersion, :releaseDate, :modified)');
		}
		$result = $prepare
				->bind(':browserId', $values['browser'])
				->bind(':branch', $values['branch'])
				->bind(':releaseVersion', $values['version'])
				->bind(':releaseDate', $releaseTime)
				->bind(':modified', time())
				->execute();
				
		if ($historyId>0) {
			$result->bind(':id', $historyId);
		}
		$result->execute();
		
		if ($result!==false) {
			echo '<br><br><div class="alert alert-success">'
				.$browsersValues[$values['browser']]
				.' '
				.$branches[$values['branch']]
				.' ver. '
				.$values['version']
				.' @ '
				.$values['date']
				.'</div>';
		}
	} 
	
	// print_r($browsers[$values['browser']]['id']);
	// print_r($values);
	
}



?>
<br>
<form id="form_add" name="form_add" method="post" action="/<?=$this->action.($historyId>0?'/'.$historyId:'')?>">
	
	Браузер<br>
	<?=$this->lib->forms->getSelect('browser', $browsersValues, $values['browser'])?>
	<br>
	
	Ветка<br>
	<?=$this->lib->forms->getSelect('branch', $branches, $values['branch'])?>
	<br>
	
	Версия<br>
	<input type="text" value="<?=$values['version']?>" id="version" name="version">
	<br>
	
	Дата<br>
	<input type="text" value="<?=$values['date']?>" id="date" name="date">
	<br>
	
	Дополнительная информация<br>
	<input type="text" value="<?=$values['note']?>" id="note" name="note">
	
	<br><br>
	
	<input type="submit" value="Отправить" id="submit">
	
</form>