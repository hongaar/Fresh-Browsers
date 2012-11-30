<?php

// http://www.opera.com/mobile/download/versions/
// http://snapshot.opera.com/windows/latest
// http://snapshot.opera.com/unix/latest
// http://snapshot.opera.com/mac/latest

// http://arc.opera.com/snapshot/

// http://www.opera.com/browser/download/?custom=yes



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
        'linux'   => 'import/opera/linux-preview.html',
        'windows' => 'import/opera/windows-preview.html',
        'osx'     => 'import/opera/mac-preview.html',
    );
    
    $release = array(
        'linux'   => 'import/opera/linux-release.html',
        'windows' => 'import/opera/windows-release.html',
        'osx'     => 'import/opera/mac-release.html',
    );
    
    $regexpPreview = '/href=\"Opera\-([0-9]+\.[0-9]+.*)(\.i386\.rpm|\.i386\.exe|\.dmg)\".*([0-9]{1,2}\-[0-9a-z]{1,3}\-[0-9]{2,4}) [0-9]{1,2}\:[0-9]{1,2}/iU';
    
    $versions = array();
    foreach ($previewFiles as $osName => $fileName) {
        if (file_exists($path.'/'.$fileName)) {
            $html = file_get_contents($path.'/'.$fileName);
            preg_match_all($regexpPreview, $html, $data);
            $versions['beta'][$osName] = array(
                'version' => $data[1][0],
                'date' => strtotime($data[3][0]),
            );
        }
    }
    return $versions;
    
}


$v = getVersionsOpera($this->_dir);

print_r($v);

return $v;