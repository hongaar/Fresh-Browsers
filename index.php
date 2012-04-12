<?php
/**
 * Nanobanano framework 
 * 
 * PHP version 5
 *
 * @copyright 2012 Dmitry Elfimov
 * @license   http://www.elfimov.ru/nanobanano/license.txt MIT License
 * @link      http://elfimov.ru/nanobanano
 *
 */
 
/**
 * main index.php
 *
 * @package  Nanobanano
 * @author   Dmitry Elfimov <elfimov@gmail.com>
 *
 */

require 'Core.php';
$core = new Core();
$core->libs = array(
    't'                => array('Translate'),
    'pdo'              => array('PDO', 'sqlite:'.$core->dir.'/versions/browsers.sqlite'),
    'db'               => array('PDOWrapper', '@pdo'),
    'session'          => array('Session'),
    'user'             => array('User', '@db', '@session'),
    'variables'        => array('Variables', '@db'),
    'browsersVersions' => array('browsersVersions', '@db'),
);
$core->libsDefaultMethods = array(
    'user'    =>    'get',
    't'       =>    't',
);
$core->languages = array(	
    'en'=>'English', 
    'ru'=>'Русский', 
);
if (isset($core->languages[$core->action])) {
	$language = $core->action;
	$core->action = $core->getAction($core->variables);
} else {
	$language = null;
}
$languagesKeys = array_keys($core->languages);
$core->libs['t'] = array('Translate', null, $language, $languagesKeys[0], $languagesKeys);

echo $core->render();
