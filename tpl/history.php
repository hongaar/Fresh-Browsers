<?php

$this->edit = false;

$this->subtitle = 'The history of web browsers.';

$branches = $this->lib->browsersVersions->getBranches();

if (isset($this->variables[0])) {
	$id = (int) $this->variables[0];
	$obj = $this->lib->db->prepare('SELECT * FROM history WHERE id=:id')
						->bind(':id', $id)
						->execute()
						->fetch();
	if ($obj!==false) {
	
		$browsers = $this->lib->browsersVersions->getBrowsers();

		?>
		<h1><?=$browsers[$obj['browserId']]['name'].' '.$obj['releaseVersion'].' ('.ucfirst($branches[$obj['branchId']]).')'?></h1>
		<p>Released: <?=date($this->lib->t('Y-m-d'), $obj['releaseDate']+3600*6)?></p>
		<?=($obj['note']!='') ? '<p>'.$obj['note'].'</p>' : ''?>
		<br>
		<a href="<?=$this->link('/history')?>">&larr; Back to history</a>
		<?php
		return true;
	} 
	
}


if (isset($_GET['branch']) && intval($_GET['branch'])>0 && isset($branches[$_GET['branch']])) {
	$branchId = intval($_GET['branch']);
} else {
	$branchId = 1;
}

$browsers = $this->lib->db->prepare('SELECT * FROM browsers ORDER BY shortName LIMIT 20')
					->execute()
					->fetchAll();
		
		
$result = $this->lib->db->prepare('SELECT * FROM history WHERE branchId=:branchId ORDER BY browserId, releaseDate DESC LIMIT 1000')
						->bind(':branchId', $branchId)
						->execute();
$history = array();
while ($browser = $result->fetch()) {
	$history[$browser['browserId']][$browser['branchId']][] = $browser;
}
?>
<div class="history">
<div class="row">

<?php
foreach ($browsers as $browserId => $browser) {
$shortName = strtolower($browser['shortName']);
?>
<div class="span2 browsers">
	<div class="browser" id="browser-<?=$shortName?>"><a href="<?=$browser['link']?>"></a></div>
	<h4><a href="<?=$browser['link']?>"><?=$browser['name']?></a></h4>
	
	<?php

	if (isset($history[$browser['id']][$branchId])) {
		$versionPrev = null;
		$out = '';
		$head = '';
		foreach ($history[$browser['id']][$branchId] as $historyObj) {
			$version = explode('.', $historyObj['releaseVersion']);
			if (isset($version[3]) || (isset($version[2]) && isset($versionPrev[2]) && !isset($version[3]) && $versionPrev[2]==$version[2])) {
				if (isset($versionPrev[2]) && ($versionPrev[2]!=$version[2] || $versionPrev[1]!=$version[1] || $versionPrev[0]!=$version[0])) {
					echo $head
						.$out
						.(!empty($head)?'</div>':'');
					$out = '';
					$head = '';
				} 
				if (!isset($versionPrev[2]) || $versionPrev[2]!=$version[2]) {
					$head .= '<div class="browser-block" id="browser-'.$shortName.'-'.$branches[$branchId].'-'.$version[0].'-'.$version[1].'-'.$version[2].'">';
					$head .= '<div class="browser-title browser-'.$branches[$branchId].'-'.$shortName.'">'
							.'<a href="#" title="Latest: '.$browser['name'].' '.$historyObj['releaseVersion'].'. Click for more">'.$version[0].'.'.$version[1].'.'.$version[2].' <span>...</span></a>'
							.' <span class="date">'.date($this->lib->t('Y-m-d'), $historyObj['releaseDate']+3600*6).'</span>'
							.'</div>';
				}
				
			} else {
				echo $head
					.$out
					.(!empty($head)?'</div>':'');
				$out = '';
				$head = '';
			}
			
			
			$out .= '<div class="browser-version browser-'.$branches[$branchId].'-'.$shortName.'">'
					.'<a href="'.$this->link('/history/'.$historyObj['id']).'" title="'.$browser['name'].' '.$historyObj['releaseVersion'].'">'.$historyObj['releaseVersion'].'</a>'
					.' <span class="date">'.date($this->lib->t('Y-m-d'), $historyObj['releaseDate']+3600*6).'</span>'
					.($this->edit ? ' <a href="'.$this->link('/edit/'.$historyObj['id']).'" class="icon-edit"></a> <a href="'.$this->link('/remove/'.$historyObj['id']).'" class="icon-remove"></a>' : '')
					.'</div>';
			$versionPrev = $version;
		}
		echo $out;
	} 
	?>
</div>
<?php
}
?>
</div>
</div>