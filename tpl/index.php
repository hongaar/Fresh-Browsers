<?php

$browsers = $this->lib->db->prepare('SELECT * FROM browsers ORDER BY shortName LIMIT 20')
							->execute()
							->fetchAll();

?>

<div class="row">

<?php
	if (file_exists($this->dir.'/export/browsers.html')) {
		include($this->dir.'/export/browsers.html');
	} else {
		echo $this->template('browsers.tpl', array('browsers'=>$browsers));
	}
?>

<div class="span2 export">

<blockquote>
<h4>Export</h4>
<ul class="unstyled">
	<li><a href="http://www.elfimov.ru/browsers/export/browsers.xml">XML</a></li>
	<li><a href="http://www.elfimov.ru/browsers/export/browsers.yaml">YAML</a></li>
	<li><a href="http://www.elfimov.ru/browsers/export/browsers.json">JSON</a></li>
	<li><a href="http://www.elfimov.ru/browsers/export/browsers.serialized">PHP serialized</a></li>
	<li><a href="http://www.elfimov.ru/browsers/export/browsers.html">HTML</a></li>
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


