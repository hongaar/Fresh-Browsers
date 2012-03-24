<?php
foreach ($browsers as $shortName => $browser) {
?>
<div class="span2 browsers">
	<div class="browser" id="browser-<?=$shortName?>"><a href="<?=$browser['link']?>"></a></div>
	<h3><a href="<?=$browser['link']?>"><?=$browser['name']?></a></h3>
<?php
	foreach ($browser as $branchName=>$branch) {
		if (is_array($branch)) {
?>
	<h4><?=ucfirst($branchName)?></h4>
	<div class="release">
		<span class="version"><?=$branch['releaseVersion']?></span>
		<span class="date"><?=$branch['releaseDate']?></span>
	</div>
<?php
		}
	}
?>
</div>
<?php
}
?>