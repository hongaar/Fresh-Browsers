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
    $first = true;
	foreach ($browser as $branchName => $branch) {
		if ($branchName != 'Dev') {
			if (is_array($branch)) {
			?>
			<div class="release<?=$first ? ' release-first' : ''?>">
			<h4><?=$this->lib->t($branchName)?></h4>
			<?php
			$first = false;
			foreach ($branch as $osName => $osBranch) {
				if ($osName != 'linux') {
				?>
				<h5><?=$osArr[$osName]?></h5>
				<div class="info">
					<span class="version"><?=$osBranch['version']?></span>
					<span class="date"><?=$osBranch['date']?></span>
				</div>
				<?php
				}
			}
			?>
			</div>
			<?php
			}
		}
	}
    ?>
</div>
<?php
}
?>