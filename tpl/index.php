<?php

	include($__dir.'/lib/translate/translate.php');
	$t = new translate();
	
	include($__dir.'/lib/PDOWrapper/PDOWrapper.php');
	
	try {
		$pdo = new PDO('sqlite:'.$__dir.'/versions/browsers.sqlite');
		$db = new PDOWrapper($pdo);
	} catch (PDOException $e) {
		echo 'Connection failed: ' . $e->getMessage();
	}

	$browsers = $db->prepare('SELECT * FROM browsers ORDER BY shortName LIMIT 10')
						->execute()
						->fetchAll();
	
?>
<div class="hero-unit">
	<h1>Fresh Browsers</h1>
	<p>Latest versions of major web browsers.</p>
</div>


<div class="row">

<?php
	foreach ($browsers as $browser) {
?>
<div class="span2 browsers">
	<div class="browser" id="browser_<?=$browser['shortName']?>"><a href="<?=$browser['link']?>"></a></div>
	<h4><a href="<?=$browser['link']?>"><?=$browser['name']?></a></h4>
	<h5><?=$browser['latestVersion']?></h5>
	<h6>(<?=$browser['latestUpdate']?>)</h6>
</div>
<?php
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