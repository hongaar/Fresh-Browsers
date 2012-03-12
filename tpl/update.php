<?php

/*
 * check new versions and update if necessary 
 *
 */

$this->mainTemplate = 'empty.tpl';

$updated = $this->lib->browsersVersions->updateVersions();

if ($updated===false) {
	echo implode('<br>', $this->lib->browsersVersions->errors);
} else 
if (!empty($updated)) {
	echo implode('<br>', $updated);
	$this->template('makeexport.tpl'); // force export for autoApproveCheck
} else {
	echo 'nothing to do';
}
