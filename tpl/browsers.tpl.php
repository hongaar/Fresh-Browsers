<?php
foreach ($browsers as $shortName => $browser) {
?>
<div class="span2 browsers">
	<div class="browser" id="browser_<?=$shortName?>"><a href="<?=$browser['link']?>"></a></div>
	<h4><a href="<?=$browser['link']?>"><?=$browser['name']?></a></h4>
	<?php
	foreach ($browser as $branchName=>$branch) {
		if (is_array($branch)) {
			?>
			<h4><?=ucfirst($branchName)?></h4>
			<h5><?=$branch['releaseVersion']?> (<?=$branch['releaseDate']?>)</h5>
			<?php
		}
	}
	?>
</div>
<?php
}
?>