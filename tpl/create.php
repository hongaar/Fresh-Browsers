<?php

$this->mainTemplate = 'empty.tpl';

$exportPath = $this->dir.'/export';

$browsersArr = $this->lib->db->prepare('SELECT * FROM browsers ORDER BY shortName LIMIT 20')
							->execute()
							->fetchAll();
$browsers = array();
foreach ($browsersArr as $browser) {
	$browsers[strtolower($browser['shortName'])] = array(
												'name'	=> $browser['name'],
												'link'	=> $browser['link'],
												'stable'=>array(
													'releaseVersion'=>	$browser['stableVersion'],
													'releaseDate'	=>	$browser['stableUpdate'],
												),
												'preview'=>array(
													'releaseVersion'=>	$browser['previewVersion'],
													'releaseDate'	=>	$browser['previewUpdate'],
												),
												'lastUpdate'	=>	$browser['__modified'],

											);
}

file_put_contents($exportPath.'/browsers.json', json_encode($browsers));

file_put_contents($exportPath.'/browsers.serialized', serialize($browsers));

$xml = new SimpleXMLElement('<browsers/>');
arrayToXML($browsers, $xml);
file_put_contents($exportPath.'/browsers.xml', $xml->asXML());

file_put_contents($exportPath.'/browsers.yaml', $this->lib->sfYaml->dump($browsers));

file_put_contents($exportPath.'/browsers-short.html', $this->template('browsers-short.tpl', array('browsers'=>$browsersArr)));

file_put_contents($exportPath.'/browsers-full.html', $this->template('browsers-full.tpl', array('browsers'=>$browsersArr)));


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
