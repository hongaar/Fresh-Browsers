<?php

if (!empty($_GET['edit'])) {
    echo $this->template('auth.php');
}

$this->edit = false;

$this->subtitle = $this->lib->t('The history of web browsers.');

$branches = $this->lib->browsersVersions->getBranches();
$browsers = $this->lib->browsersVersions->getBrowsers();
$oses = $this->lib->browsersVersions->getOSes();

if (isset($this->variables[0])) {
	$id = (int) $this->variables[0];
	$obj = $this->lib->db->prepare('SELECT * FROM history WHERE id=:id')
        ->bind(':id', $id)
        ->execute()
        ->fetch();
	if ($obj!==false) {
		?>
		<h1><?=$browsers[$obj['browserId']]['name'].' '.$obj['version'].' ('.ucfirst($branches[$obj['branchId']]).')'?></h1>
		<p><?=$this->lib->t('Released')?>: <?=date($this->lib->t('Y-m-d'), $obj['date'])?></p>
		<?=($obj['note']!='') ? '<p>'.$obj['note'].'</p>' : ''?>
		<br>
		<a href="<?=$this->link('/'.$this->lib->t->language.'/history')?>">&larr; <?=$this->lib->t('Back to history')?></a>
		<?php
		return true;
	}
}


$branchId = 1;
$osId = 1;
$vars = array();
if (isset($this->variables[0])) {
    $vars[] = $this->variables[0];
}
if (isset($this->variables[1])) {
    $vars[] = $this->variables[1];
}

foreach ($vars as $var) {
    $temp = array_search($var, $branches);
    if ($temp !== false) {
        $branchId = $temp;
    } else {
        $temp = $this->lib->browsersVersions->getOSId($var);
        if ($temp !== false) $osId = $temp;
    }
}

$branch = $branches[$branchId];

		
$result = $this->lib->db->prepare('SELECT * FROM history WHERE branchId=? AND osId=? ORDER BY `date` DESC, __modified DESC LIMIT 1000')
						->execute(array($branchId, $osId));
$history = array();
$yearMonths = array();
while ($browser = $result->fetch()) {
	$releaseDate = $browser['date'];
	$year = date('Y', $releaseDate);
	$month = date('n', $releaseDate);
	$history[$year][$browser['browserId']][$month][] = $browser;
	if (empty($yearMonths[$year])) $yearMonths[$year] = array();
	if (!in_array($month, $yearMonths[$year])) $yearMonths[$year][] = $month;
}



?>
<div class="history">

<ul class="nav nav-pills">
<?php
    foreach ($branches as $id => $name) {
?>
    <li<?=$branchId==$id?' class="active"':''?>><a href="<?=$this->link('/'.$this->lib->t->language.'/'.$this->action.'/'.$name)?>"><?=$this->lib->t($name)?></a></li>
<?php
    }
?>
</ul>
<ul class="nav nav-pills">
<?php
    foreach ($oses as $id => $name) {
?>
    <li class="os<?=$osId==$id?'active"':''?>"><a href="<?=$this->link('/'.$this->lib->t->language.'/'.$this->action.'/'.$branch.'/'.$name[0])?>"><?=$this->lib->t($name[1])?></a></li>
<?php
    }
?>
</ul>

<table>
<tr>
<?php
foreach ($browsers as $browserId => $browser) {
	$shortName = strtolower($browser['shortName']);
?>
	<th class="span2 browsers browser-<?=$shortName?>">
		<div class="browser" id="browser-<?=$shortName?>"><a href="<?=$browser['link']?>"></a></div>
		<h4><a href="<?=$browser['link']?>"><?=$browser['name']?></a></h4>
	</th>
<?php
}
?>
	<th class="span2 browsers browsers-year">&nbsp;</th>
</tr>


<?php


foreach ($history as $year => $yearHistory) {

?>
<tr class="row-year year-<?=$year?>">
<?php

	foreach ($browsers as $browserId => $browser) {
		$browser = $browsers[$browserId];
		$shortName = strtolower($browser['shortName']);
?>
<td class="span2 browsers browser-<?=$shortName?>">
<?php
		if (!empty($yearHistory[$browserId])) {
			$browserHistory = $yearHistory[$browserId];
			

			$versionPrev = null;
			$out = '';
			$head = '';
			$n = 0;

			foreach ($yearMonths[$year] as $month) {
				
				if (!empty($browserHistory[$month])) {
					$monthObj = $browserHistory[$month];
					foreach ($monthObj as $historyObj) {
						$version = explode('.', $historyObj['version']);
						 
						if (isset($version[3]) || (isset($version[2]) && isset($versionPrev[2]) && !isset($version[3]) && $versionPrev[2]==$version[2])) {
							if (isset($versionPrev[2]) && ($versionPrev[2]!=$version[2] || $versionPrev[1]!=$version[1] || $versionPrev[0]!=$version[0])) {
								echo $n>1 ? (!empty($head)?$head.$out.'</div>':$out) : $out;
								$out = '';
								$head = '';
								$n = 0;
							} 
							if (!isset($versionPrev[2]) || $versionPrev[2]!=$version[2] || $versionPrev[1]!=$version[1] || $versionPrev[0]!=$version[0]) {
								$head .= '<div class="browser-title browser-'.$branch.'-'.$shortName.'">'
										.'<a href="#" title="Latest: '.$browser['name'].' '.$historyObj['version'].'. Click for more">'.$version[0].'.'.$version[1].'.'.$version[2].'<span>...</span></a>'
										.' <span class="date">'.date($this->lib->t('Y-m-d'), $historyObj['date']).'</span>'
										.'</div>';
								$head .= '<div class="browser-block" id="browser-'.$shortName.'-'.$branch.'-'.$version[0].'-'.$version[1].'-'.$version[2].'">';
							}
						} else {
							echo $n>1 ? (!empty($head)?$head.$out.'</div>':$out) : $out;
							$out = '';
							$head = '';
							$n = 0;
						} 
						$out .= '<div class="browser-version browser-'.$branch.'-'.$shortName.'">'
								.'<a href="'.$this->link('/'.$this->lib->t->language.'/history/'.$historyObj['id']).'" title="'.$browser['name'].' '.$historyObj['version'].'">'.$historyObj['version'].'</a>'
								.' <span class="date">'.date($this->lib->t('Y-m-d'), $historyObj['date']).'</span>'
								.($this->edit ? ' <a href="'.$this->link('/edit/'.$historyObj['id']).'" class="icon-edit"></a> <a href="'.$this->link('/remove/'.$historyObj['id']).'" class="icon-remove"></a>' : '')
								.'</div>';
						$n++;
						$versionPrev = $version;
					}
				} else {
					$out .= '&nbsp;<br>';
				}
			}
			echo $n > 1 ? (!empty($head) ? $head . $out . '</div>' : $out) : $out;
		} else {
			echo '&nbsp;<br>';
		}
?>
</td>
<?php
	}
?>

<td class="span2 browsers browsers-year"><?=$year?></td>
</tr> <?php /* class="row" */ ?>
<?php
}
?>
</table>


</div>