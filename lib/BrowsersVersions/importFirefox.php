<?php

// http://hg.mozilla.org/%s/raw-file/tip/config/milestone.txt
// nightly: "mozilla-central",
// aurora: "releases/mozilla-aurora",
// beta: "releases/mozilla-beta",
// release: "releases/mozilla-release"

function getVersionsFirefox($path) {
    $files = array(
        'nightly' => 'mozilla-central.txt',
        'aurora'  => 'releases_mozilla-aurora.txt',
        'beta'    => 'releases_mozilla-beta.txt',
        'release' => 'releases_mozilla-release.txt'
    );
    
    $ver = false;
    foreach ($files as $branch => $fileName) {
        if (file_exists($path.'/'.$filePart)) {
        $file = file($path.'/'.$filePart);
            foreach ($file as $s) {
                if (!empty($s) && $s{0}!='#' && preg_match('/^\d+\.\d+[a-z0-1]*[0-9a-z\.]*/i', $s)) {
                    $ver = $s;
                    break;
                }
            }
            $osName = 'windows'; // how to find out?
            $versions[$branch][$osName] = array(
                'version' => $ver,
                'date' => time(),
            );
        }
    }

}



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

$v = getVersionsFirefox($this->_dir); // 'http://omahaproxy.appspot.com/all.json'

$links = $this->getWikiLinks();
$v['stable']['android'] = $this->_parseWikiText(file_get_contents($this->_dir.'/chrome_stable_android.txt'), $links['chrome']['regexp'], 'stable', 'android');

return $v;