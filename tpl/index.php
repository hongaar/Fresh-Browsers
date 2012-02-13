<?php

$browsers = $this->lib->db->prepare('SELECT * FROM browsers ORDER BY shortName LIMIT 20')
							->execute()
							->fetchAll();

?>
<div class="hero-unit">
	<h1>Fresh Browsers</h1>
	<p>Latest versions of major web browsers.</p>
</div>


<div class="row">

<?php
	if (file_exists($this->dir.'/export/browsers-full.html')) {
		include($this->dir.'/export/browsers-full.html');
	} else {
		echo $this->template('browsers-full.tpl', array('browsers'=>$browsers));
	}
?>

<div class="span2">
	<h4>Mobile browsers</h4>
	<ul>
		<li>Opera Mobile</li>
		<li>Opera Mini</li>
		<li>Android Browser</li>
		<li>iPhone</li>
		<li>iPad</li>
	</ul>
</div>



</div>