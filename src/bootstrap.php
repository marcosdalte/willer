<?php

include_once("define.php");
include_once("Core/Util.php");
include_once("Core/System.php");
include_once("url.php");

use Core\Util;
use Core\System;

define("HTTP_PATH",Util::get($_GET,"http_path",null));
define("DISPLAY_ERRORS",1);
define("ERROR_REPORTING",E_ALL);
define("TIMEZONE","America/Sao_Paulo");
define("ROOT_PATH",__DIR__);
define("HTTP_TYPE","http");
define("PUBLIC_PATH",Util::str("%s://%s/public",[HTTP_TYPE,URL_SITE]));
define("URL_BASE",Util::str("%s://%s",[HTTP_TYPE,URL_SITE]));
define("REQUEST_METHOD",Util::get($_SERVER,"REQUEST_METHOD",null));
define("URL_NOT_FOUND",Util::str("%s/404.html",[URL_BASE,]));
define("QUERY_LIMIT_ROW",15);
define("SESSION_LIMIT",10);
define("DB_DEFAULT","db_default");

const LIB_PATH = [
    ROOT_PATH."/vendor/twig/twig/lib",
];

const DATABASE_INFO = [
    DB_DEFAULT => [
        "DB_DRIVER" => DB_DEFAULT_DRIVER,
        "DB_HOST" => DB_DEFAULT_HOST,
        "DB_NAME" => DB_DEFAULT_NAME,
        "DB_USER" => DB_DEFAULT_USER,
        "DB_PASSWORD" => DB_DEFAULT_PASSWORD,
        "DB_PORT" => DB_DEFAULT_PORT,
        "DB_AUTOCOMMIT" => 0,
        "DB_DEBUG" => 0,
    ],
];

System::appReady($URL);
