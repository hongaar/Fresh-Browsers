<?php

$this->mainTemplate = 'empty.tpl';

$exportPath = $this->dir.'/export';

$browsers = $this->lib->browsersVersions->getVersions();

foreach ($browsers as $browser) {
	$name = strtolower($browser['shortName']);
	$browsersExport[$name] = array(
									'name'	=> $browser['name'],
									'link'	=> $browser['link'],
									'stable'=>array(
										'releaseVersion'=>	$versions[$name]['stable']['releaseVersion'],
										'releaseDate'	=>	date('Y-m-d', $versions[$name]['stable']['releaseDate']),
									),
									'preview'=>array(
										'releaseVersion'=>	$versions[$name]['preview']['releaseVersion'],
										'releaseDate'	=>	date('Y-m-d', $versions[$name]['preview']['releaseDate']),
									),
									'lastUpdate'	=>	date('Y-m-d H:i:s', time()),
								);
}

file_put_contents($exportPath.'/browsers.json', json_encode($browsersExport));

file_put_contents($exportPath.'/browsers.serialized', serialize($browsersExport));

$xml = new SimpleXMLElement('<browsers/>');
arrayToXML($browsersExport, $xml);
file_put_contents($exportPath.'/browsers.xml', $xml->asXML());

file_put_contents($exportPath.'/browsers.yaml', $this->lib->sfYaml->dump($browsersExport));

file_put_contents($exportPath.'/browsers.html', $this->template('browsers.tpl', array('browsers'=>$browsers)));


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
