<?php

// latest firefox releases
// http://www.mozilla.org/en-US/firefox/all.html
// http://www.mozilla.org/en-US/products/download.html
// http://releases.mozilla.org/pub/mozilla.org/firefox/releases/latest/
// https://ftp.mozilla.org/pub/mozilla.org/mobile/releases/latest/android/en-US/

// latest firefox aurora
// http://www.mozilla.org/en-US/firefox/all-aurora.html
// http://www.mozilla.org/en-US/mobile/aurora/
//
// https://ftp.mozilla.org/pub/mozilla.org/firefox/nightly/latest-mozilla-aurora/
// https://ftp.mozilla.org/pub/mozilla.org/mobile/nightly/latest-mozilla-aurora-android/en-US/

// beta
// http://www.mozilla.org/en-US/firefox/all-beta.html
// 

// http://hg.mozilla.org/%s/raw-file/tip/config/milestone.txt
// nightly: "mozilla-central",
// aurora: "releases/mozilla-aurora",
// beta: "releases/mozilla-beta",
// release: "releases/mozilla-release"

function getVersionsFirefoxFromConfig($path) {
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

function getVersionsFirefox($path) {
    
    $osReplace = array(
        'win' => 'windows',
        'w32' => 'windows',
        'lin' => 'linux',
        'mac' => 'osx',
        'cros' => 'android',
    );
    
    $files = array(
        'release' => 'import/firefox/release.html',
        'aurora'  => 'import/firefox/aurora.html',
        'beta'    => 'import/firefox/beta.html',
    );
    
    $android = array(
        'release' => 'import/firefox/android-release.html',
        'aurora'  => 'import/firefox/android-aurora.html',
    );
    
    $regexp = '/href=\".*download.*firefox-([0-9]+\.[0-9\.a-z]+)&.*os=([a-z0-9]+)&.*lang=en/iU';
    $regexpAndroid = '/href=\"fennec-([0-9]+\.[0-9a-z]+)\..*\.apk\".*([0-9]{1,2}\-[0-9a-z]{1,3}\-[0-9]{2,4}) [0-9]{1,2}\:[0-9]{1,2}/iU';
    
    $versions = array();
    foreach ($files as $branch => $fileName) {
        if (file_exists($path.'/'.$fileName)) {
            $html = file_get_contents($path.'/'.$fileName);
            preg_match_all($regexp, $html, $data);
            foreach ($data[1] as $n => $ver) {
                $osName = $data[2][$n];
                $osName = isset($osReplace[$osName]) ? $osReplace[$osName] : $osName;
                $versions[$branch][$osName] = array(
                    'version' => $ver,
                    'date' => time(),
                );
            }

        }
    }
    
    foreach ($android as $branch => $fileName) {
        if (file_exists($path.'/'.$fileName)) {
            $html = file_get_contents($path.'/'.$fileName);
            preg_match_all($regexpAndroid, $html, $data);
            $osName = 'android';
            if (!isset($data[1][0])) return false;
            $time = strtotime($data[1][2]);
            if ($time===false) return false;
            $versions[$branch][$osName] = array(
                'version' => $data[1][0],
                'date' => $time,
            );
        }
    }
}



function get

$v = getVersionsFirefox($this->_dir);

return $v;