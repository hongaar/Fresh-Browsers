<?php

return array(
			'chrome' =>
					array(
							'stable' => 'http://en.wikipedia.org/w/index.php?action=raw&title=Template:Latest_stable_software_release/Google_Chrome',
							'preview' => 'http://en.wikipedia.org/w/index.php?action=raw&title=Template:Latest_preview_software_release/Google_Chrome',
							'regexp' => array(
												'version'	=>	'/\|[ ]{0,1}latest[ _]{0,1}release[ _]{0,1}version = (\d+\.\d+.\d+.\d*)/i',
												'date'		=>	'/\|[ ]{0,1}latest[ _]{0,1}release[ _]{0,1}date = \{\{(.*)[|\/](\d{4})[|\/](\d{1,2})[|\/](\d{1,2})\}\}/i',
												'version_preview'	=>	'/\|[ ]{0,1}latest[ _]{0,1}release[ _]{0,1}version =[\'\<\>a-z\ ]*Dev[\'\<\>a-z\ ]*(\d+\.\d+.\d+.\d*)/i',
												'date_preview'		=>	'/Dev.*\|[ ]{0,1}latest[ _]{0,1}release[ _]{0,1}date *= *\{\{(.*)[|\/](\d{4})[|\/](\d{1,2})[|\/](\d{1,2})\}\}/is'
											)
						),
						
			'firefox' =>
					array(
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

			'ie' =>
					array(
							'stable' => 'http://en.wikipedia.org/w/index.php?action=raw&title=Template:Latest_stable_software_release/Internet_Explorer',
							'preview' => 'http://en.wikipedia.org/w/index.php?action=raw&title=Template:Latest_preview_software_release/Internet_Explorer',
							'regexp' => array(
												'version'	=>	'/\|[ ]{0,1}latest[ _]{0,1}release[ _]{0,1}version = (\d+\.\d+)/i',
												'date'		=>	'/\|[ ]{0,1}latest[ _]{0,1}release[ _]{0,1}date = \{\{(.*)[|\/](\d{4})[|\/](\d{1,2})[|\/](\d{1,2})\}\}/i'
											)
						),

			'safari' =>
					array(
							'stable' => 'http://en.wikipedia.org/w/index.php?action=raw&title=Template:Latest_stable_software_release/Safari',
							'preview' => 'http://en.wikipedia.org/w/index.php?action=raw&title=Template:Latest_preview_software_release/Safari',
							'regexp' => array(
												'version'	=>	'/\|[ ]{0,1}latest[ _]{0,1}release[ _]{0,1}version = (\d+\.\d+.\d+)/i',
												'date'		=>	'/\|[ ]{0,1}latest[ _]{0,1}release[ _]{0,1}date = \{\{(.*)[|\/](\d{4})[|\/](\d{1,2})[|\/](\d{1,2})\}\}/i'
											)
						),
			);