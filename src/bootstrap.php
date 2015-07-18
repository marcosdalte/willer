<?php

include_once("Core/Util.php");
include_once("Core/System.php");
include_once("url.php");

define("REQUEST_URI",$_SERVER["REQUEST_URI"]);
define("DEBUG",true);
define("ROOT_PATH",__DIR__);

Core\System::appReady($url);
