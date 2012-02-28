<?php

$this->edit = false;


if (isset($this->variables[0])) {
	$id = (int) $this->variables[0];
	$obj = $this->lib->db->prepare('SELECT * FROM history WHERE id=:id')
						->bind(':id', $id)
						->execute()
						->fetch();
	if ($obj!==false) {
	
		$browsers = $this->lib->browsersVersions->getBrowsers();

		?>
		<h1><?=$browsers[$obj['browserId']]['name'].' '.$obj['releaseVersion'].' ('.$this->lib->browsersVersions->branches[$obj['branch']].')'?></h1>
		<p>Released: <?=date('Y-m-d', $obj['releaseDate'])?></p>
		<?=($obj['note']!='') ? '<p>'.$obj['note'].'</p>' : ''?>
		<br>
		<a href="/history">&larr; Back to history</a>
		<?php
		return true;
	} 
	
}


if (isset($_GET['branch']) && intval($_GET['branch'])>0 && isset($this->lib->browsersVersions->branches[$_GET['branch']])) {
	$branchId = intval($_GET['branch']);
} else {
	$branchId = 1;
}

$browsers = $this->lib->db->prepare('SELECT * FROM browsers ORDER BY shortName LIMIT 20')
					->execute()
					->fetchAll();
/*$browsers = array();
foreach ($browsers as $browser) {
	$browsers[$browser['id']][] = $browser;
}*/


		
		
$historyArr = $this->lib->db->prepare('SELECT * FROM history WHERE branch=:branch ORDER BY browserId, releaseDate DESC LIMIT 1000')
						->bind(':branch', $branchId)
						->execute()
						->fetchAll();
$history = array();
foreach ($historyArr as $historyObj) {
	$history[$historyObj['browserId']][$historyObj['branch']][] = $historyObj;
}
?>

<div class="row">

<?php
foreach ($browsers as $browser) {
//	$shortName = strtolower($browser['shortName']);
?>
<div class="span2 browsers">
	<div class="browser" id="browser_<?=$browser['shortName']?>"><a href="<?=$browser['link']?>"></a></div>
	<h4><a href="<?=$browser['link']?>"><?=$browser['name']?></a></h4>
	<!-- h5><?=$browser['stableVersion']?></h5>
	<h6>(<?=date('Y-m-d',$browser['stableUpdate'])?>)</h6 -->
	<br>
	<ul class="unstyled">
		<?php
		if (isset($history[$browser['id']][$branchId])) {
			foreach ($history[$browser['id']][$branchId] as $historyObj) {
			?>
				<li><a href="/history/<?=$historyObj['id']?>" title="<?=$browser['name'].' '.$historyObj['releaseVersion']?>"><?=$historyObj['releaseVersion']?></a> <span class="date"><?=date('Y-m-d',$historyObj['releaseDate'])?></span><?=$this->edit?' <a href="'.$this->link('/edit/'.$historyObj['id']).'" class="icon-edit"></a> <a href="'.$this->link('/remove/'.$historyObj['id']).'" class="icon-remove"></a>':''?></li>
			<?php
			}
		}
		?>
	</ul>
</div>
<?php
}
?>
</div>