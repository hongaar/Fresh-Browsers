<?php

$this->mainTemplate = 'empty.tpl';

$t = $this->lib->t;

if (isset($this->variables[1])) {
	if ($this->variables[0]=='yes') {
		$browser = $this->lib->browsersVersions->approveNewVersion($this->variables[1]);
		if ($browser===false) {
			echo implode('<br>', $this->lib->browsersVersions->errors);
		} else {
			echo 'APPROVED: '. $browser['browserId'].' '.$browser['branchId'].' '.$browser['releaseVersion'].' '.date($t->t('Y-m-d'),$browser['releaseDate']);
			$this->template('makeexport.tpl');
		}
	} else 
	if ($this->variables[0]=='no') {
		$browser = $this->lib->browsersVersions->deleteNewVersion($this->variables[1]);
		if ($browser===false) {
			echo implode('<br>', $this->lib->browsersVersions->errors);
		} else {
			echo 'DELETED: '. $browser['browserId'].' '.$browser['branchId'].' '.$browser['releaseVersion'].' '.date($t->t('Y-m-d'),$browser['releaseDate']);
		}
	}
}





