<?php

/*
 * check new versions and update if necessary 
 *
 */

$this->mainTemplate = 'empty.tpl';

// set true if you want to approve new versions
$this->lib->browsersVersions->doNotApprove = true;
// change these emails to correct ones
$this->lib->browsersVersions->approveEmailFrom = 'browsers@elfimov.ru';
$this->lib->browsersVersions->approveEmailTo = 'elfimov@gmail.com';

$this->lib->browsersVersions->approveLink = $this->link('/browsers/approve', true);

$updated = $this->lib->browsersVersions->updateVersions();

if ($updated===false) {
	echo implode('<br>', $this->lib->browsersVersions->errors);
} else 
if (!empty($updated)) {
	echo implode('<br>', $updated);
	$this->template('makeexport.tpl'); // force export for autoApproveCheck() & doNotCheck=true
} else {
	echo 'nothing to do';
}
