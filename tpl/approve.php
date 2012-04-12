<?php

$this->mainTemplate = 'empty.tpl.php';

if (isset($this->variables[1])) {
	if ($this->variables[0]=='yes') {
		$browser = $this->lib->browsersVersions->approveNewVersion($this->variables[1]);
		if ($browser===false) {
			echo implode('<br>', $this->lib->browsersVersions->errors);
		} else {
			echo 'APPROVED: '. $browser['browserId'].' '.$browser['branchId'].' '.$browser['releaseVersion'].' '.date($this->lib->t('Y-m-d'),$browser['releaseDate']);
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
			echo 'DELETED: '. $browser['browserId'].' '.$browser['branchId'].' '.$browser['releaseVersion'].' '.date($this->lib->t('Y-m-d'),$browser['releaseDate']);
		}
	}
}





