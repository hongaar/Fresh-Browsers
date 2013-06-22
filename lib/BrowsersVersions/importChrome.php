<?php

function getVersionsChrome($source) 
{
    $osReplace = array(
        'win' => 'windows',
        'w32' => 'windows',
        'mac' => 'osx',
        'android' => 'android',
		'ios' => 'ios',
    );
    $info = json_decode(file_get_contents($source), true);
    $versions = array();
    foreach ($info as $os) {
        foreach ($os['versions'] as $ver) {
            $osName = isset($osReplace[$os['os']]) ? $osReplace[$os['os']] : $os['os'];
            $versions[$ver['channel']][$osName] = array(
                'version' => $ver['version'],
                'date' => strtotime($ver['date']),
            );
        }
    }
    return $versions;
}

if (empty($v['stable']['android'])) {
    $v = getVersionsChrome($this->_dir.'/import/chrome/all.json'); // 'http://omahaproxy.appspot.com/all.json'
    $links = $this->getWikiLinks();
//    $v['stable']['android'] = $this->_parseWikiText(file_get_contents($this->_dir.'/import/chrome/stable_android.txt'), $links['chrome']['regexp'], 'stable', 'android');
}

return $v;