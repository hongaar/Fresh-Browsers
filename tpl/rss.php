<?php

$this->mainTemplate = 'rss.tpl.php';

$browsers = $this->lib->browsersVersions->getBrowsers();
$branches = $this->lib->browsersVersions->getBranches();

$historyArr = $this->lib->db->prepare('SELECT * FROM history ORDER BY releaseDate DESC, __modified DESC LIMIT 30')
						->execute()
						->fetchAll();

foreach ($historyArr as $obj) {
$link = $this->link('/history/'.$obj['id'], true);
?>
<item>
<title><?=$browsers[$obj['browserId']]['name'].' '.$obj['releaseVersion'].' ('.ucfirst($branches[$obj['branchId']]).')'?></title>
<link><?=$link?></link>
<description>
<?=date($this->lib->t('Y-m-d'), $obj['releaseDate']).' - '.$browsers[$obj['browserId']]['name'].' '.$obj['releaseVersion'].' ('.ucfirst($branches[$obj['branchId']]).')'?>
<?=($obj['note']!='') ? ' NOTE: '.$obj['note'] : ''?>
</description>
<pubDate><?=date('r', $obj['__modified'])?></pubDate>
</item>
<?php
}