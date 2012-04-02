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

$core->initRouter();

echo $core->start();




