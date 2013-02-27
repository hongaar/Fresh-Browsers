<?php

$this->mainTemplate = 'empty.tpl.php';

if (isset($this->variables[1])) {
    $browsers = $this->lib->browsersVersions->getBrowsers();
    $branches = $this->lib->browsersVersions->getBranches();
    $oses = $this->lib->browsersVersions->getOSes();
	if ($this->variables[0]=='yes') {
		$browser = $this->lib->browsersVersions->approveNewVersion($this->variables[1]);
		if ($browser===false) {
			echo implode('<br>', $this->lib->browsersVersions->errors);
		} else {
			echo 'APPROVED: '. $browsers[$browser['browserId']]['name'].' '.$branches[$browser['branchId']].' '.$browser['version'].' ('.$oses[$browser['osId']][1].') '.date($this->lib->t('Y-m-d'), $browser['date']);
			echo '<hr>';
			$this->template('makeexport.tpl.php');
			echo $this->template('twitter.php');
		}
	} else 
	if ($this->variables[0]=='no') {
		$browser = $this->lib->browsersVersions->deleteNewVersion($this->variables[1]);
		if ($browser===false) {
			echo implode('<br>', $this->lib->browsersVersions->errors);
		} else {
			echo 'DELETED: '. $browsers[$browser['browserId']]['name'].' '.$branches[$browser['branchId']].' '.$browser['version'].' ('.$oses[$browser['osId']][1].') '.date($this->lib->t('Y-m-d'), $browser['date']);
		}
	}
}





