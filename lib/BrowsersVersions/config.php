<?php

return array(

    'os'       => array(
                            1 => array('windows', 'Windows'),
                            2 => array('linux',   'Linux'),
                            3 => array('ios',     'iOS'),
                            4 => array('android', 'Android'),
                            5 => array('osx',     'Mac OS X'),
                        ),
    
	'branches' => array(
							1	=>	'stable',
                            2	=>	'beta',
							3	=>	'preview',
							4	=>	'dev',
							99	=>	'lts', // long term support
						),
						
	'browsers' => array(
							1	=> array(
										'name'			=> 'Google Chrome',
										'shortName'		=> 'Chrome',
										'link'			=> 'http://www.google.com/chrome',
                                        'branches'      => array(1 => 'stable', 2 => 'beta', 3 => 'dev', 4 => 'canary'),
                                        'os'            => array('windows', 'linux', 'android', 'ios', 'osx')
										),

							2	=> array(
										'name'			=> 'Mozilla Firefox',
										'shortName'		=> 'Firefox',
										'link'			=> 'http://www.mozilla.com/firefox',
                                        'branches'      => array(1 => 'release', 2 => 'beta', 3 => 'aurora', 4 => 'nightly'),
                                        'os'            => array('windows', 'linux', 'android', 'osx')
										),

							3	=> array(
										'name'			=> 'Internet Explorer',
										'shortName'		=> 'IE',
										'link'			=> 'http://www.microsoft.com/ie',
                                        'branches'      => array(1 => 'stable', 3 => 'preview'),
                                        'os'            => array('windows')
										),

							4	=> array(
										'name'			=> 'Opera',
										'shortName'		=> 'Opera',
										'link'			=> 'http://www.opera.com/browser/',
                                        'branches'      => array(1 => 'stable', 2 => 'beta'),
                                        'os'            => array('windows', 'linux', 'osx')
										),

							5	=> array(
										'name'			=> 'Apple Safari',
										'shortName'		=> 'Safari',
										'link'			=> 'http://www.apple.com/safari/',
                                        'branches'      => array(1 => 'stable', 2 => 'beta'),
                                        'os'            => array('windows', 'ios', 'osx')
										),
	)
);