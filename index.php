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
						't'		=>	array('translate'),		// ^language should be replaced by $core->option['language']
						'pdo'	=>	array('PDO', 'sqlite:'.$core->dir.'/versions/browsers.sqlite'),
						'db'	=>	array('PDOWrapper', '@pdo'),
						'browsersVersions'	=>	array('browsersVersions', '@db'),
						'variables'	=>	array('variables', '@db'),
					);
$core->libsDefaultMethods = array(
					'user'	=>	'get',
					't'		=>	't',
					);
$core->options = array('language'=>array('ru', 'en', 'de'));
$core->controller();

if (!empty($core->options) && !empty($core->options['language'])) {
	$core->libs['t'] = array('translate', null, $core->options['language'], 'en', array('ru', 'en', 'de'));
}

$core->model();

echo $core->view();;


