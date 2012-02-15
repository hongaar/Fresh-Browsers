<?php

$this->edit = false;


$branches = array(
	-1	=>	'---',
	1	=>	'Stable',
	2	=>	'LTS',
	3	=>	'Preview',
	4	=>	'Dev',
);

if (isset($_GET['branch']) && intval($_GET['branch'])>0 && isset($branches[$_GET['branch']])) {
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
				<li><?=$historyObj['releaseVersion']?> <span class="date"><?=date('Y-m-d',$historyObj['releaseDate'])?></span><?=$this->edit?' <a href="'.$this->link('/edit/'.$historyObj['id']).'" class="icon-edit"></a> <a href="'.$this->link('/remove/'.$historyObj['id']).'" class="icon-remove"></a>':''?></li>
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