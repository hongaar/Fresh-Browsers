<?php
	foreach ($browsers as $browser) {
?>
<div class="span2 browsers">
	<div class="browser" id="browser_<?=$browser['shortName']?>"><a href="<?=$browser['link']?>"></a></div>
	<h4><a href="<?=$browser['link']?>"><?=$browser['name']?></a></h4>
	<h4>Stable</h4>
	<h5><?=$browser['stableVersion']?> (<?=date('Y-m-d', $browser['stableUpdate'])?>)</h5>
	<h4>Preview</h4>
	<h5><?=$browser['previewVersion']?> (<?=date('Y-m-d', $browser['previewUpdate'])?>)</h5>
</div>
<?php
	}
?>