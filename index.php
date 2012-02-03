<?php

/*
 * nanobanano framework 
 * http://
 *
 * Copyright (c) 2011 by Dmitry Elfimov
 * Released under the MIT License.
 * http://www.opensource.org/licenses/mit-license.php
 *
 * Date: 2011-12-16
 */


if (isset($_GET['c']) && !empty($_GET['c'])) {
	$__tpl = preg_replace('/[^a-zA-Z0-9_]/', '_', substr($_GET['c'], 0, 255));
} else {
	$__tpl = 'index';
}

$__dir = dirname(__FILE__);
$__tpl = $__dir.'/tpl/'.$__tpl.'.php';

if (!file_exists($__tpl)) {
	header("HTTP/1.0 404 Not Found");
	header("Status: 404 Not Found");
	$__tpl = $__dir.'/tpl/error404.php';
}

require($__dir.'/tpl/index.tpl.php');
