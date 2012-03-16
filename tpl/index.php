<?php

$browsers = $this->lib->db->prepare('SELECT * FROM browsers ORDER BY shortName LIMIT 20')
							->execute()
							->fetchAll();

?>

<div class="row">

<?php
/*	if (file_exists($this->dir.'/export/browsers.html')) {
		echo file_get_contents($this->dir.'/export/browsers.html');
	} else {
		echo $this->template('browsers.tpl', array('browsers'=>$browsers));
	}
	*/
$versions = $this->lib->browsersVersions->getVersions();
$browsers = $this->lib->browsersVersions->getBrowsers();
$branches = $this->lib->browsersVersions->getBranches();

$export = array();
foreach ($browsers as $browserId => $browser) {			// all browsers
	foreach ($branches as $branchId=>$branchName) {		// all branches
		$branchName = ucfirst($branchName);
		if (isset($versions[$browserId][$branchId])) {	// check if we have version for this browser-branch
			$browserName = strtolower($browser['shortName']);
			if (!isset($export[$browserName])) {				// create export array if this browser is not in it yet 
				$export[$browserName] = array(	
					'name'			=> $browser['name'],
					'link'			=> $browser['link'],
					'lastUpdate'	=> date($this->lib->t('Y-m-d H:i:s'), time()),
				);
			}
			$export[$browserName][$branchName] = array(
				'releaseVersion'=>	$versions[$browserId][$branchId]['releaseVersion'],
				'releaseDate'	=>	date($this->lib->t('Y-m-d'), $versions[$browserId][$branchId]['releaseDate']),
			);
		}
	}
}
echo $this->template('browsers.tpl', array('browsers'=>$export))
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


