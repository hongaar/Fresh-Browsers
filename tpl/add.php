<?php


$browsersArr = $lib->db->prepare('SELECT * FROM browsers ORDER BY shortName LIMIT 20')
					->execute()
					->fetchAll();
					
$browsers = array();
$browsersValues = array(-1=>'---');

foreach ($browsersArr as $browser) {
	$shortName = strtolower($browser['shortName']);
	$browsers[$shortName] = $browser;
	$browsersValues[$shortName] = $browser['name'];
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
$time = 0;
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
					$time = strtotime($newValue);
					if ($time>0) {
						$value = date('Y-m-d', $time);
					}					
					break;
					
				default:
					$value = html_entity_decode($newValue, null, 'UTF-8');
			}
		}
	}
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