<?php

require(dirname(__DIR__).'/vendor/autoload.php');
require('Core/Util.php');
require('Core/System.php');
require('url.php');

// error_reporting(E_ALL|E_STRICT);
// ini_set('display_errors',1);

define('DEBUG',true);
define('URL_PREFIX','');
define('REQUEST_URI',$_SERVER['REQUEST_URI']);
define('ROOT_PATH',__DIR__);
define('DATABASE_PATH',ROOT_PATH.'/database.json');
define('DATABASE','default');

new Core\System($url);