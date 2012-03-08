<?php

$this->mainTemplate = 'empty.tpl';

$exportPath = $this->dir.'/export';

$versions = $this->lib->browsersVersions->getVersions();
$browsers = $this->lib->browsersVersions->getBrowsers();
$branches = $this->lib->browsersVersions->getBranches();

$export = array();

foreach ($browsers as $browserId => $browser) {			// all browsers
	foreach ($branches as $branchId=>$branchName) {		// all branches
		if (isset($versions[$browserId][$branchId])) {	// check if we have version for this browser-branch
			$browserName = strtolower($browser['shortName']);
			if (!isset($export[$browserName])) {				// create export array if this browser is not in it yet 
				$export[$browserName] = array(	
					'name'			=> $browser['name'],
					'link'			=> $browser['link'],
					'lastUpdate'	=> date('Y-m-d H:i:s', time()),
				);
			}
			$export[$browserName][$branchName] = array(
				'releaseVersion'=>	$versions[$browserId][$branchId]['releaseVersion'],
				'releaseDate'	=>	date('Y-m-d', $versions[$browserId][$branchId]['releaseDate']),
			);
		}
	}
}

file_put_contents($exportPath.'/browsers.json', json_encode($export));

file_put_contents($exportPath.'/browsers.serialized', serialize($export));

$xml = new SimpleXMLElement('<browsers/>');
arrayToXML($export, $xml);
file_put_contents($exportPath.'/browsers.xml', $xml->asXML());

file_put_contents($exportPath.'/browsers.yaml', $this->lib->sfYaml->dump($export));

file_put_contents($exportPath.'/browsers.html', $this->template('browsers.tpl', array('browsers'=>$export)));


function arrayToXML($array, &$xml) {
	foreach($array as $key => $value) {
		if(is_array($value)) {
			if (!is_numeric($key)) {
				$subnode = $xml->addChild($key);
				arrayToXML($value, $subnode);
			} else {
				arrayToXML($value, $xml);
			}
		} else {
			$xml->addChild($key, $value);
		}
	}
}
