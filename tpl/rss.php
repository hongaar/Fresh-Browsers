<?php

$this->mainTemplate = 'rss.tpl';

$browsers = $this->lib->browsersVersions->getBrowsers();

$historyArr = $this->lib->db->prepare('SELECT * FROM history ORDER BY releaseDate DESC LIMIT 30')
						->execute()
						->fetchAll();

foreach ($historyArr as $obj) {
$link = 'http://elfimov.ru/browsers/history/'.$obj['id'];
?>
<item>
<title><?=$browsers[$obj['browserId']]['name'].' '.$obj['releaseVersion'].' ('.$this->lib->browsersVersions->branches[$obj['branch']].')'?></title>
<link><?=$link?></link>
<description>
<?=date('Y-m-d', $obj['releaseDate']).' - '.$browsers[$obj['browserId']]['name'].' '.$obj['releaseVersion'].' ('.$this->lib->browsersVersions->branches[$obj['branch']].')'?>
<?=($obj['note']!='') ? ' NOTE: '.$obj['note'] : ''?>
</description>
<pubDate><?=date('r', $obj['__modified'])?></pubDate>
</item>
<?php
}