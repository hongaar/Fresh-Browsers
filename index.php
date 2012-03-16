<?php

/*
 * nanobanano framework 
 * https://github.com/Groozly/nanobanano
 *
 * Copyright (c) 2011-2012 by Dmitry Elfimov
 * Released under the MIT License.
 * http://www.opensource.org/licenses/mit-license.php
 *
 * Date: 2012-02-10
 */

require('core.php');
$core = new core();
$core->libs = array(
						't'		=>	array('translate'),
						'pdo'	=>	array('PDO', 'sqlite:'.$core->dir.'/versions/browsers.sqlite'),
						'db'	=>	array('PDOWrapper', '@pdo'),
						'browsersVersions'	=>	array('browsersVersions', '@db'),
					);
$core->libsDefaultMethods = array(
					'user'	=>	'get',
					't'		=>	't',
					);
echo $core->render();


