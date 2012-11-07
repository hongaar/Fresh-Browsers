<?php

/*
 * check new versions and update if necessary 
 *
 */

$this->mainTemplate = 'empty.tpl.php';

// set false if you want to approve new versions
$this->lib->browsersVersions->doNotApprove = false;
// change these emails to correct ones
$this->lib->browsersVersions->approveEmailFrom = 'browsers@elfimov.ru';
$this->lib->browsersVersions->approveEmailTo = 'elfimov@gmail.com';

$this->lib->browsersVersions->approveLink = $this->link('/approve', true);

$updated = $this->lib->browsersVersions->updateVersions();

if ($updated===false) {
	echo implode('<br>', $this->lib->browsersVersions->errors);
} else 
if (!empty($updated)) {
	echo implode('<br>', $updated);
	$this->template('makeexport.tpl.php'); // force export for autoApproveCheck() & doNotCheck=true
    $this->template('twitter.php');
} else {
	echo 'nothing to do';
}
