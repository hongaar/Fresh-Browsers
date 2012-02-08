<?php

/*
 * nanobanano framework 
 * https://github.com/Groozly/nanobanano
 *
 * Copyright (c) 2011 by Dmitry Elfimov
 * Released under the MIT License.
 * http://www.opensource.org/licenses/mit-license.php
 *
 * Date: 2012-02-07
 */


if (isset($_GET['c']) && !empty($_GET['c'])) {
	$__tpl = preg_replace('/[^a-zA-Z0-9_]/', '_', substr($_GET['c'], 0, 255));
} else {
	$__tpl = 'index';
}

$__dir = dirname(__FILE__);
$__tpl = $__dir.'/tpl/'.$__tpl.'.php';


include($__dir.'/lib/lib.php');
$lib = new lib(array(
				't'			=>	array('translate'),
//				'browsers'	=>	array('browsersVersions'),
				'pdo'		=>	array('PDO', 'sqlite:'.$__dir.'/versions/browsers.sqlite'),
				'db'		=>	array('PDOWrapper', '@pdo'),
			));
			

if (!file_exists($__tpl)) {
	header("HTTP/1.0 404 Not Found");
	header("Status: 404 Not Found");
	$__tpl = $__dir.'/tpl/error404.php';
}

require($__dir.'/tpl/index.tpl.php');
