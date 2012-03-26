<?php

$this->edit = false;

if (!$this->edit) {
	return false;
}

$browsers = $this->lib->browsersVersions->getBrowsers();
					
$browsers = array();
$browsersValues = array(-1=>'---');

foreach ($browsersArr as $browserId => $browser) {
	$shortName = strtolower($browser['shortName']);
	$browsers[$shortName] = $browser;
	$browsersValues[$browserId] = $browser['name'];
}

$branches = array(
	-1	=>	'---',
	1	=>	'Stable',
	2	=>	'LTS',
	3	=>	'Preview',
	4	=>	'Dev',
);


$values = array(
	'browser'	=>	-1,
	'branch'	=>	-1,
	'version'	=>	'',
	'date'		=>	'',
	'note'		=>	'',
);
$releaseTime = 0;

if (isset($_POST)) {
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
		$result = $lib->db->prepare('INSERT INTO history (browserId, branch, releaseVersion, releaseDate, __modified) VALUES (:browserId, :branch, :releaseVersion, :releaseDate, :modified)')
				->bind(':browserId', $values['browser'])
				->bind(':branch', $values['branch'])
				->bind(':releaseVersion', $values['version'])
				->bind(':releaseDate', $releaseTime)
				->bind(':modified', time())
				->execute();
				
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
<form id="form_add" name="form_add" method="post" action="/add">
	
	Браузер<br>
	<?=$lib->forms->getSelect('browser', $browsersValues, $values['browser'])?>
	<br>
	
	Ветка<br>
	<?=$lib->forms->getSelect('branch', $branches, $values['branch'])?>
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