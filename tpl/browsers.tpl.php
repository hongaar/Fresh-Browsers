<?php

$osArr = array();
foreach ($oses as $osId => $os) {
    $osArr[$os[0]] = $os[1];
}

foreach ($browsers as $shortName => $browser) {
?>
<div class="span2 browsers">
	<div class="browser" id="browser-<?=$shortName?>"><a href="<?=$browser['link']?>"></a></div>
	<h3><a href="<?=$browser['link']?>"><?=$browser['name']?></a></h3>
<?php
	foreach ($browser as $branchName => $branch) {
		if (is_array($branch)) {
?>
	<h4><?=$this->lib->t($branchName)?></h4>
	<div class="release">
<?php
		foreach ($branch as $osName => $osBranch) {
?>
		<h5><?=$osArr[$osName]?></h5>
		<span class="version"><?=$osBranch['version']?></span>
		<span class="date"><?=$osBranch['date']?></span>
<?php
		}
?>
	</div>
<?php
		}
	}
?>
</div>
<?php
}
?>