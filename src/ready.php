<?php

require(dirname(__DIR__).'/vendor/autoload.php');

define('ROOT_PATH',__DIR__);
define('UPKEEP_PATH',dirname(__DIR__).'/upkeep');
define('DATABASE_PATH',ROOT_PATH.'/config/database.json');

$system = new Core\System();
$system->ready();
