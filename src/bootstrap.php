<?php

require('Core/Util.php');
require('Core/System.php');
require('url.php');

define('REQUEST_URI',$_SERVER['REQUEST_URI']);
define('DEBUG',true);
define('ROOT_PATH',__DIR__);

Core\System::appReady($url);