<?php

// http://www.opera.com/mobile/download/versions/
// http://snapshot.opera.com/windows/latest
// http://snapshot.opera.com/unix/latest
// http://snapshot.opera.com/mac/latest

// http://arc.opera.com/snapshot/

// http://www.opera.com/browser/download/?custom=yes

// http://www.opera.com/browser/download/?os=windows



function getVersionsOpera($path) 
{
    
    $osReplace = array(
        'win' => 'windows',
        'w32' => 'windows',
        'lin' => 'linux',
        'mac' => 'osx',
        'cros' => 'android',
    );
    
    $previewFiles = array(
        'linux'   => 'import/opera/linux-preview.txt',
        'windows' => 'import/opera/windows-preview.txt',
        'osx'     => 'import/opera/mac-preview.txt',
    );
//        '/href=\"Opera\-([0-9]+\.[0-9]+.*)(\.i386\.rpm|\.i386\.exe|\.dmg)\".*([0-9]{1,2}\-[0-9a-z]{1,3}\-[0-9]{2,4}) [0-9]{1,2}\:[0-9]{1,2}/iU';
    $previewRegexp = array(
        'windows' => '/Location\:.*\/windows\/[0-9a-z]+\_([0-9]+\.[0-9]+)(\-[0-9a-z]*)\/Opera.*\.exe/iU',
        'osx' => '/Location\:.*\/mac\/[0-9a-z]+\_([0-9]+\.[0-9]+)(\-[0-9a-z]*)\/Opera.*\.dmg/iU',
        'linux' => '/Location\:.*\/unix\/[0-9a-z]+\_([0-9]+\.[0-9]+)([\-0-9a-z]+)/i',
        // 'linux'   => '/\[[ ]*[0-9]+ (.*)\][ ]*linux\/([0-9]{2,6})\/Opera[-_]{1}[0-9]+.*/iU',
        // 'mac'     => '/\[[ ]*[0-9]+ (.*)\][ ]*mac\/([0-9]{2,6})\/Opera[-_]{1}[0-9\.]+.*\.dmg/iU',
    );
    
    $releaseFile = 'import/opera/release.txt';
    $releaseRegexp = array(
        'windows' => '/\[[ ]*[0-9]+ (.*)\][ ]*win\/([0-9]{2,6})\/en\/Opera[-_]{1}[0-9]+.*\.exe/iU',
        'linux'   => '/\[[ ]*[0-9]+ (.*)\][ ]*linux\/([0-9]{2,6})\/Opera[-_]{1}[0-9]+.*\.deb/iU',
        'mac'     => '/\[[ ]*[0-9]+ (.*)\][ ]*mac\/([0-9]{2,6})\/Opera[-_]{1}[0-9\.]+.*\.dmg/iU',
//        'beta'    => '/href=\".*firefox-([0-9]+\.[0-9\.a-z]+)&.*os=([a-z0-9]+)&.*lang=en/iU',
//        'aurora'  => '/href=\".*firefox-([0-9]+\.[0-9\.a-z]+)\..*.*en.*(win32|mac|linux)/iU',
    );
    
    
    $versions = array();
    
    foreach ($previewFiles as $osName => $fileName) {
        if (file_exists($path.'/'.$fileName)) {
            $html = file_get_contents($path.'/'.$fileName);
            preg_match_all($previewRegexp[$osName], $html, $data);
            $osName = isset($osReplace[$osName]) ? $osReplace[$osName] : $osName;
            if (!empty($data) && !empty($data[1]) && !empty($data[1][0])) {
                $verAdd = empty($data[2][0]) ? '' : '.'.trim($data[2][0], " \t\n\r-");
                $versions['beta'][$osName] = array(
                    'version' => $data[1][0].$verAdd,
                    'date' => mktime(12, 0, 0),
                );
            }
        }
    }
    
    
    if (file_exists($path.'/'.$releaseFile)) {
        $txt = file_get_contents($path.'/'.$releaseFile);
        foreach ($releaseRegexp as $osName => $regexp) {
            preg_match_all($regexp, $txt, $data);
            if (!empty($data) && !empty($data[2])) {
                foreach ($data[1] as &$dt) {
                    $dt = strtotime($dt);
                }
                
                arsort($data[1]);
                reset($data[1]);
                $key = key($data[1]);
                
                $osName = isset($osReplace[$osName]) ? $osReplace[$osName] : $osName;
                if (!empty($data[2][$key])) {
                    $ver = substr($data[2][$key], 0, 2) . '.' . substr($data[2][$key], 2);
                    $versions['release'][$osName] = array(
                        'version' => $ver,
                        'date' => $data[1][$key],
                    );
                }
            }
        }
    }
    
    return $versions;
    
}


$v = getVersionsOpera($this->_dir);

// print_r($v);

return $v;