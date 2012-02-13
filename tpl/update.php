<?php


$browsers = $this->lib->db->prepare('SELECT * FROM browsers ORDER BY shortName LIMIT 20')
					->execute()
					->fetchAll();

$versions = $this->lib->browsersVersions->getVersions();

$update = $this->lib->db->prepare('UPDATE browsers SET stableVersion=:stableVersion, stableUpdate=:stableUpdate, previewVersion=:previewVersion, previewUpdate=:previewUpdate WHERE id=:id');
foreach ($browsers as $browser) {
	$name = strtolower($browser['shortName']);
	if (isset($versions[$name]['stable']) && $versions[$name]['stable']['releaseVersion']>$browser['stableVersion']) {
		$update
			->bind(':stableVersion', $versions[$name]['stable']['releaseVersion'])
			->bind(':stableUpdate',  $versions[$name]['stable']['releaseDate'])
			->bind(':id', $browser['id'])
			->execute();
	}
	
	if (isset($versions[$name]['preview']) && $versions[$name]['preview']['releaseVersion']>$browser['previewVersion']) {
		$update
			->bind(':previewVersion', $versions[$name]['preview']['releaseVersion'])
			->bind(':previewUpdate',  $versions[$name]['preview']['releaseDate'])
			->bind(':id', $browser['id'])
			->execute();
	}
}

echo '<pre>';
print_r($browsers);
echo '<hr>';
print_r($versions);
echo '</pre>';