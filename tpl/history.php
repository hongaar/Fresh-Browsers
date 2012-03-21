<?php

$this->edit = false;

$branches = $this->lib->browsersVersions->getBranches();

if (isset($this->variables[0])) {
	$id = (int) $this->variables[0];
	$obj = $this->lib->db->prepare('SELECT * FROM history WHERE id=:id')
						->bind(':id', $id)
						->execute()
						->fetch();
	if ($obj!==false) {
	
		$browsers = $this->lib->browsersVersions->getBrowsers();

		?>
		<h1><?=$browsers[$obj['browserId']]['name'].' '.$obj['releaseVersion'].' ('.ucfirst($branches[$obj['branchId']]).')'?></h1>
		<p>Released: <?=date($this->lib->t('Y-m-d'), $obj['releaseDate'])?></p>
		<?=($obj['note']!='') ? '<p>'.$obj['note'].'</p>' : ''?>
		<br>
		<a href="<?=$this->link('/history')?>">&larr; Back to history</a>
		<?php
		return true;
	} 
	
}


if (isset($_GET['branch']) && intval($_GET['branch'])>0 && isset($branches[$_GET['branch']])) {
	$branchId = intval($_GET['branch']);
} else {
	$branchId = 1;
}

$browsers = $this->lib->db->prepare('SELECT * FROM browsers ORDER BY shortName LIMIT 20')
					->execute()
					->fetchAll();
		
		
$result = $this->lib->db->prepare('SELECT * FROM history WHERE branchId=:branchId ORDER BY browserId, releaseDate DESC LIMIT 1000')
						->bind(':branchId', $branchId)
						->execute();
$history = array();
while ($browser = $result->fetch()) {
	$history[$browser['browserId']][$browser['branchId']][] = $browser;
}
?>
<div class="history">
<div class="row">

<?php
foreach ($browsers as $browserId => $browser) {
$shortName = strtolower($browser['shortName']);
?>
<div class="span2 browsers">
	<div class="browser" id="browser-<?=$shortName?>"><a href="<?=$browser['link']?>"></a></div>
	<h4><a href="<?=$browser['link']?>"><?=$browser['name']?></a></h4>
	<?php
	if (isset($history[$browser['id']][$branchId])) {
		foreach ($history[$browser['id']][$branchId] as $historyObj) {
			?>
			<div class="browser-version browser-<?=$branches[$branchId].'-'.$shortName.'-'.$version[0]?>"><a href="<?=$this->link('/history/'.$historyObj['id'])?>" title="<?=$browser['name'].' '.$historyObj['releaseVersion']?>"><?=$historyObj['releaseVersion']?></a> <span class="date"><?=date($this->lib->t('Y-m-d'), $historyObj['releaseDate'])?></span><?=$this->edit?' <a href="'.$this->link('/edit/'.$historyObj['id']).'" class="icon-edit"></a> <a href="'.$this->link('/remove/'.$historyObj['id']).'" class="icon-remove"></a>':''?></div>
			<?php
		}
	}
	?>
</div>
<?php
}
?>
</div>
</div>