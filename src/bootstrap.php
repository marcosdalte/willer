<?php

require(dirname(__DIR__).'/vendor/autoload.php');
require('Core/Util.php');
require('Core/System.php');
require('url.php');

define('URL_PREFIX','/');
define('REQUEST_URI',$_SERVER['REQUEST_URI']);
define('DEBUG',true);
define('ROOT_PATH',__DIR__);

new Core\System($url);

