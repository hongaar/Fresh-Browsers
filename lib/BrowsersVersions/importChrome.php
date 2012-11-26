<?php

function getVersions($source) {
    $osReplace = array(
        'win' => 'windows',
        'w32' => 'windows',
        'mac' => 'osx',
        'cros' => 'android',
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

$v = getVersions($this->_dir.'/chrome_all.json'); // 'http://omahaproxy.appspot.com/all.json'

$links = $this->getWikiLinks();
$v['stable']['android'] = $this->_parseWikiText(file_get_contents($this->_dir.'/chrome_stable_android.txt'), $links['chrome']['regexp'], 'stable', 'android');

return $v;