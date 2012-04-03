<?php

/*
 * nanobanano framework 
 * https://github.com/Groozly/nanobanano
 *
 * Copyright (c) 2011-2012 by Dmitry Elfimov
 * Released under the MIT License.
 * http://www.opensource.org/licenses/mit-license.php
 *
 * Date: 2012-04-03
 */

require('core.php');

$core = new core();

$core->libs = array(
						't'		=>	array('translate'),
						'pdo'	=>	array('PDO', 'sqlite:'.$core->dir.'/versions/browsers.sqlite'),
						'db'	=>	array('PDOWrapper', '@pdo'),
						'browsersVersions'	=>	array('browsersVersions', '@db'),
						'variables'	=>	array('variables', '@db'),
					);
$core->libsDefaultMethods = array(
						'user'	=>	'get',
						't'		=>	't',
						);
$core->languages = array(	
						'en'=>'English', 
						'ru'=>'Русский', 
//						'de'=>'Deutsch',
					);
$languagesKeys = array_keys($core->languages);

if (isset($core->languages[$core->action])) {
	$language = $core->action;
	$core->action = $core->getAction(array_shift($core->variables));
} else {
	$language = null;
}
$core->libs['t'] = array('translate', null, $language, $languagesKeys[0], $languagesKeys);

echo $core->render();




