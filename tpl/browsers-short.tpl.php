<?php
	foreach ($browsers as $browser) {
?>
<div class="span2 browsers">
	<div class="browser" id="browser_<?=$browser['shortName']?>"><a href="<?=$browser['link']?>"></a></div>
	<h4><a href="<?=$browser['link']?>"><?=$browser['name']?></a></h4>
	<h5><?=$browser['stableVersion']?></h5>
	<h6>(<?=date('Y-m-d', $browser['stableUpdate'])?>)</h6>
</div>
<?php
	}
?>