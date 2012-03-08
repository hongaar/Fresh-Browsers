<?php

$this->mainTemplate = 'rss.tpl';

$browsers = $this->lib->browsersVersions->getBrowsers();
$branches = $this->lib->browsersVersions->getBranches();

$historyArr = $this->lib->db->prepare('SELECT * FROM history ORDER BY releaseDate DESC LIMIT 30')
						->execute()
						->fetchAll();

foreach ($historyArr as $obj) {
$link = 'http://elfimov.ru/browsers/history/'.$obj['id'];
?>
<item>
<title><?=$browsers[$obj['browserId']]['name'].' '.$obj['releaseVersion'].' ('.$branches[$obj['branchId']].')'?></title>
<link><?=$link?></link>
<description>
<?=date('Y-m-d', $obj['releaseDate']).' - '.$browsers[$obj['browserId']]['name'].' '.$obj['releaseVersion'].' ('.$branches[$obj['branchId']].')'?>
<?=($obj['note']!='') ? ' NOTE: '.$obj['note'] : ''?>
</description>
<pubDate><?=date('r', $obj['__modified'])?></pubDate>
</item>
<?php
}