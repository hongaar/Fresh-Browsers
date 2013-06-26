<?php

$this->mainTemplate = 'rss.tpl.php';

$browsers = $this->lib->browsersVersions->getBrowsers();
$branches = $this->lib->browsersVersions->getBranches();
$oses = $this->lib->browsersVersions->getOSes();

$historyArr = $this->lib->db->prepare('SELECT * FROM history ORDER BY `date` DESC, __modified DESC LIMIT 50')
						->execute()
						->fetchAll();

foreach ($historyArr as $obj) {
$link = $this->link('/history/'.$obj['id'], true);
?>
<item>
<title><?=$browsers[$obj['browserId']]['name'].' '.$obj['version'].' ('.ucfirst($branches[$obj['branchId']]).', '.$oses[$obj['osId']][1].')'?></title>
<link><?=$link?></link>
<description>
<?=date($this->lib->t('Y-m-d'), $obj['date']).' - '.$browsers[$obj['browserId']]['name'].' '.$obj['version'].' ('.ucfirst($branches[$obj['branchId']]).', '.$oses[$obj['osId']][1].')'?>
<?=($obj['note']!='') ? ' NOTE: '.$obj['note'] : ''?>
</description>
<pubDate><?=date('r', $obj['__modified'])?></pubDate>
</item>
<?php
}