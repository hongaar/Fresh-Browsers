<div class="row">

<?php

$this->lib->browsersVersions->dateFormat = $this->lib->t('Y-m-d');
$this->lib->browsersVersions->timeFormat = $this->lib->t('H:i:s');
	
$export = $this->lib->browsersVersions->getExport();

echo $this->template('browsers.tpl.php', array('browsers'=>$export));
?>

<div class="span2 export">

<blockquote>
<h4><?=$this->lib->t('Export')?></h4>
<ul class="unstyled">
	<li><a href="<?=$this->link('/export/browsers.xml')?>">XML</a></li>
	<li><a href="<?=$this->link('/export/browsers.yaml')?>">YAML</a></li>
	<li><a href="<?=$this->link('/export/browsers.json')?>">JSON</a></li>
	<li><a href="<?=$this->link('/export/browsers.serialized')?>">PHP serialized</a></li>
	<li><a href="<?=$this->link('/export/browsers.html')?>">HTML</a></li>
	<li><a href="<?=$this->link('/rss')?>">RSS</a></li>
	<li><a href="http://twitter.com/FreshBrowsers">Twitter</a></li>
</ul>
</blockquote>

	<!--h4>Mobile browsers</h4>
	<ul>
		<li>Opera Mobile</li>
		<li>Opera Mini</li>
		<li>Android Browser</li>
		<li>iPhone</li>
		<li>iPad</li>
	</ul-->
</div>

</div>
