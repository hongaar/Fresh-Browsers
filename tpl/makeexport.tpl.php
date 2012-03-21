<?php

// export new versions to html
$exportPath = $this->dir.'/export';

$export = $this->lib->browsersVersions->getExport();

file_put_contents($exportPath.'/browsers.json', json_encode($export));

file_put_contents($exportPath.'/browsers.serialized', serialize($export));

file_put_contents($exportPath.'/browsers.xml', arrayToXML($export, '<browsers/>')->asXML());

file_put_contents($exportPath.'/browsers.yaml', $this->lib->sfYaml->dump($export));

file_put_contents($exportPath.'/browsers.html', $this->template('browsers.tpl', array('browsers'=>$export)));


function arrayToXML($array, $xml) {
	if (!is_object($xml)) {
		$xml = new SimpleXMLElement($xml);
	}
	foreach ($array as $key => $value) {
		if (is_array($value)) {
			if (!is_numeric($key)) {
				$subnode = $xml->addChild($key);
				$subnode = arrayToXML($value, $subnode);
			} else {
				$xml = arrayToXML($value, $xml);
			}
		} else {
			$xml->addChild($key, $value);
		}
	}
	return $xml;
}