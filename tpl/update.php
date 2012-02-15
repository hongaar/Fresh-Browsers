<?php

/*
 * update browsers, history & export files
 *
 */

$this->mainTemplate = 'empty.tpl';

$forceExport = false;

$exportPath = $this->dir.'/export';

$browsers = $this->lib->db
					->prepare('SELECT * FROM browsers ORDER BY shortName LIMIT 100')
					->execute()
					->fetchAll();

$versions = $this->lib->browsersVersions->getVersions();

$this->lib->browsersVersions->updateVersions();


$historyArr = $this->lib->db->prepare('SELECT * FROM history GROUP BY browserId, branch ORDER BY browserId, releaseDate DESC LIMIT 100')
						->execute()
						->fetchAll();
					
$history = array();
foreach ($historyArr as $historyObj) {
	$history[$historyObj['browserId']][$historyObj['branch']] = $historyObj;
}

$browsersExport = array();
$updatedNames = array();

$updateBrowsers = $this->lib->db->prepare('UPDATE browsers SET stableVersion=:stableVersion, stableUpdate=:stableUpdate, previewVersion=:previewVersion, previewUpdate=:previewUpdate, __modified=:modified WHERE id=:id');

$insertHistory = $this->lib->db->prepare('INSERT INTO history (browserId, branch, releaseVersion, releaseDate, __modified) VALUES (:browserId, :branch, :releaseVersion, :releaseDate, :modified)');

foreach ($browsers as $browser) {
	$name = strtolower($browser['shortName']);
	if ($versions[$name]['stable']['releaseDate']>$browser['stableUpdate']
		|| $versions[$name]['preview']['releaseDate']>$browser['previewUpdate']) {
		$updateBrowsers->bind(':stableVersion', $versions[$name]['stable']['releaseVersion'])
			->bind(':stableUpdate',  $versions[$name]['stable']['releaseDate'])
			->bind(':previewVersion', $versions[$name]['preview']['releaseVersion'])
			->bind(':previewUpdate',  $versions[$name]['preview']['releaseDate'])
			->bind(':modified', time())
			->bind(':id', $browser['id'])
			->execute();
		$updatedNames[] = $browser['name'];
	}
	
	$historyBoth = (!isset($history[$browser['id']]) || (!isset($history[$browser['id']][1]) && !isset($history[$browser['id']][3])));
	$historyStable = $historyBoth || !isset($history[$browser['id']][1]) || ($history[$browser['id']][1]['releaseDate']<$versions[$name]['stable']['releaseDate']);
	$historyPreview = $historyBoth || !isset($history[$browser['id']][3]) || ($history[$browser['id']][3]['releaseDate']<$versions[$name]['preview']['releaseDate']) ;

	if ($historyStable) {
		$insertHistory
			->bind(':browserId', $browser['id'])
			->bind(':branch', 1)
			->bind(':releaseVersion', $versions[$name]['stable']['releaseVersion'])
			->bind(':releaseDate', $versions[$name]['stable']['releaseDate'])
			->bind(':modified', time())
			->execute();
	}
	if ($historyPreview) {
		$insertHistory
			->bind(':browserId', $browser['id'])
			->bind(':branch', 3)
			->bind(':releaseVersion', $versions[$name]['preview']['releaseVersion'])
			->bind(':releaseDate', $versions[$name]['preview']['releaseDate'])
			->bind(':modified', time())
			->execute();
	}

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

if (!empty($updatedNames)) {
	echo 'Updated: '.implode(', ', $updatedNames);
}

if (!empty($updatedNames) || $forceExport) {
	file_put_contents($exportPath.'/browsers.json', json_encode($browsersExport));

	file_put_contents($exportPath.'/browsers.serialized', serialize($browsersExport));

	$xml = new SimpleXMLElement('<browsers/>');
	arrayToXML($browsersExport, $xml);
	file_put_contents($exportPath.'/browsers.xml', $xml->asXML());

	file_put_contents($exportPath.'/browsers.yaml', $this->lib->sfYaml->dump($browsersExport));

//	file_put_contents($exportPath.'/browsers-short.html', $this->template('browsers-short.tpl', array('browsers'=>$browsers)));

	file_put_contents($exportPath.'/browsers.html', $this->template('browsers-full.tpl', array('browsers'=>$browsers)));
}




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
