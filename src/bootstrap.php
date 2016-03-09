<?php

require(dirname(__DIR__).'/vendor/autoload.php');

define('DEBUG',0);
define('URL_PREFIX','willer/willer/');
define('REQUEST_URI',$_SERVER['REQUEST_URI']);
define('ROOT_PATH',__DIR__);
define('DATABASE_PATH',ROOT_PATH.'/Config/database.json');
define('DATABASE','default');
define('QUERY_LIMIT',30);

new Core\System();
