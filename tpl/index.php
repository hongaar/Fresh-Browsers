<?php

$browsers = $this->lib->db->prepare('SELECT * FROM browsers ORDER BY shortName LIMIT 20')
							->execute()
							->fetchAll();

?>

<div class="row">

<?php
	if (file_exists($this->dir.'/export/browsers.html')) {
		echo file_get_contents($this->dir.'/export/browsers.html');
	} else {
		echo $this->template('browsers.tpl', array('browsers'=>$browsers));
	}
?>

<div class="span2 export">

<blockquote>
<h4>Export</h4>
<ul class="unstyled">
	<li><a href="<?=$this->link('/export/browsers.xml')?>">XML</a></li>
	<li><a href="<?=$this->link('/export/browsers.yaml')?>">YAML</a></li>
	<li><a href="<?=$this->link('/export/browsers.json')?>">JSON</a></li>
	<li><a href="<?=$this->link('/export/browsers.serialized')?>">PHP serialized</a></li>
	<li><a href="<?=$this->link('/export/browsers.html')?>">HTML</a></li>
	<li><a href="<?=$this->link('/rss')?>">RSS</a></li>
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


