<?php

$this->edit = false;


$branches = array(
	-1	=>	'---',
	1	=>	'Stable',
	2	=>	'LTS',
	3	=>	'Preview',
	4	=>	'Dev',
);

$browsers = $this->lib->db->prepare('SELECT * FROM browsers ORDER BY shortName LIMIT 20')
					->execute()
					->fetchAll();
/*$browsers = array();
foreach ($browsers as $browser) {
	$browsers[$browser['id']][] = $browser;
}*/


		
		
$historyArr = $this->lib->db->prepare('SELECT * FROM history ORDER BY browserId, releaseDate DESC LIMIT 1000')
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
		if (isset($history[$browser['id']][1])) {
			foreach ($history[$browser['id']][1] as $historyObj) {
			?>
				<li><?=$historyObj['releaseVersion']?> <span class="date"><?=date('Y-m-d',$historyObj['releaseDate'])?></span><?=$this->edit?' <a href="/edit/'.$historyObj['id'].'" class="icon-edit"></a> <a href="/remove/'.$historyObj['id'].'" class="icon-remove"></a>':''?></li>
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