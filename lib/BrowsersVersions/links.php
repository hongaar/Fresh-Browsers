<?php

return array(
/*
	'chrome' => array(
		'releases' => array(
			'windows' => array(
				'stable' => 'http://en.wikipedia.org/w/index.php?action=raw&title=Template:Latest_stable_software_release/Google_Chrome',
				'preview' => 'http://en.wikipedia.org/w/index.php?action=raw&title=Template:Latest_preview_software_release/Google_Chrome',
			),
		),
		// regexp key format:
		// <version|date>[_BranchName][_OSName]
		'regexp' => array(
			'version'	=>	'/\|[ ]{0,1}latest[ _]{0,1}release[ _]{0,1}version = (\d+\.\d+.\d+.\d*)/i',
			'date'		=>	'/\|[ ]{0,1}latest[ _]{0,1}release[ _]{0,1}date = \{\{.*[|\/]((\d{4})[|\/](\d{1,2})[|\/](\d{1,2}))\}\}/i',
			'version_preview'	=>	'/\|[ ]{0,1}latest[ _]{0,1}release[ _]{0,1}version =[\'\<\>a-z\ ]*Dev[\'\<\>a-z\ ]*(\d+\.\d+.\d+.\d*)/i',
			'date_preview'		=>	'/Dev.*\|[ ]{0,1}latest[ _]{0,1}release[ _]{0,1}date *= *\{\{.*[|\/]((\d{4})[|\/](\d{1,2})[|\/](\d{1,2}))\}\}/is',
			'version_stable_android'	=>	'/\|[ ]{0,1}latest[ _]{0,1}release[ _]{0,1}version =.*Android[\'\<\>\/a-z\ ]*(\d+\.\d+\.\d+[\.0-9]*) \(ARM\)/i',
		)
	),

	'firefox' => array(
		'stable' => 'http://en.wikipedia.org/w/index.php?action=raw&title=Template:Latest_stable_software_release/Firefox',
		'preview' => 'http://en.wikipedia.org/w/index.php?action=raw&title=Template:Latest_preview_software_release/Firefox',
		'regexp' => array(
							'version'	=>	'/\|[ ]{0,1}latest[ _]{0,1}release[ _]{0,1}version = (\d+\.\d+[\. \w]*)/i',
							'date'		=>	'/\|[ ]{0,1}latest[ _]{0,1}release[ _]{0,1}date = \{\{(.*)[|\/](\d{4})[|\/](\d{1,2})[|\/](\d{1,2})\}\}/i'
						)
	),

	'opera' =>
			array(
					'stable' => 'http://en.wikipedia.org/w/index.php?action=raw&title=Template:Latest_stable_software_release/Opera',
					'preview' => 'http://en.wikipedia.org/w/index.php?action=raw&title=Template:Latest_preview_software_release/Opera',
					'regexp' => array(
										'version'	=>	'/\|[ ]{0,1}latest[ _]{0,1}release[ _]{0,1}version = (\d+\.\d+[\(\. \w\)]*)/i',
										'date'		=>	'/\|[ ]{0,1}latest[ _]{0,1}release[ _]{0,1}date = \{\{(.*)[|\/](\d{4})[|\/](\d{1,2})[|\/](\d{1,2})\}\}/i'
									)
				),
*/
	'ie' =>
        array(
            'releases' => array(
                'windows' => array(
                    'stable' => 'http://en.wikipedia.org/w/index.php?action=raw&title=Template:Latest_stable_software_release/Internet_Explorer',
//                    'preview' => 'http://en.wikipedia.org/w/index.php?action=raw&title=Template:Latest_preview_software_release/Internet_Explorer',
                ),
                'winphone' => array(
                    'stable' => 'http://en.wikipedia.org/w/index.php?action=raw&title=Template:Latest_stable_software_release/Internet_Explorer_Mobile',
                ),
            ),
            'regexp' => array(
                'version'	=>	'/\|[ ]{0,1}latest[ _]{0,1}release[ _]{0,1}version = (\d+\.\d+[\.\d+]*)/i',
                'date'		=>	'/\|[ ]{0,1}latest[ _]{0,1}release[ _]{0,1}date = \{\{.*[|\/]((\d{4})[|\/](\d{1,2})[|\/](\d{1,2}))\}\}/i'
            )
        ),

	'safari' =>
        array(
            'releases' => array(
                'osx' => array(
					'stable' => 'http://en.wikipedia.org/w/index.php?action=raw&title=Template:Latest_stable_software_release/Safari',
//					'preview' => 'http://en.wikipedia.org/w/index.php?action=raw&title=Template:Latest_preview_software_release/Safari',
                ),
            ),
            'regexp' => array(
                'version'	=>	'/\|[ ]{0,1}latest[ _]{0,1}release[ _]{0,1}version = (\d+\.\d+.\d+)/i',
                'date'		=>	'/\|[ ]{0,1}latest[ _]{0,1}release[ _]{0,1}date = \{\{.*[|\/]((\d{4})[|\/](\d{1,2})[|\/](\d{1,2}))\}\}/i'
            )
        ),

	);