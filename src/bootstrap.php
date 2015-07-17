<?php

include_once("Core/Util.php");
include_once("Core/System.php");
include_once("url.php");

define("REQUEST_URI",$_SERVER["REQUEST_URI"]);
define("ROOT_PATH",__DIR__);

$database = Core\Util::loadJsonFile(ROOT_PATH."/database.json",true);
print_r($database);
const A = 1;
const DATABASE = "sdfsdfsdf".A;

// exit("BB");
// const VENDOR = Core\Util::loadJsonFile(ROOT_PATH."/vendor.json");

// exit("AAA");

// Core\System::appReady($url);