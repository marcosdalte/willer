<?php

include_once("define.php");
include_once("Core/Util.php");
include_once("Core/System.php");
include_once("url.php");

use Core\Util;
use Core\System;

define("HTTP_PATH",Util::get($_GET,"http_path",null));
define("VERSION","0.0.3");
define("DISPLAY_ERRORS",1);
define("ERROR_REPORTING",E_ALL);
define("TIMEZONE","America/Sao_Paulo");
define("ROOT_PATH",__DIR__);
define("HTTP_TYPE","http");
define("PUBLIC_PATH",Util::str("%s://%s/public",[HTTP_TYPE,URL_SITE]));
define("TEMPLATE_PATH",Util::str("%s://%s/public/theme",[HTTP_TYPE,URL_SITE]));
define("URL_BASE",Util::str("%s://%s",[HTTP_TYPE,URL_SITE]));
define("PATH_404","404.html");
define("URL_NOT_FOUND",Util::str("%s/%s",[URL_BASE,PATH_404]));
define("QUERY_LIMIT_ROW",15);
define("SESSION_LIMIT",10);
define("API_AUTH_KEY",API_KEY);
define("API_AUTH_USER",API_USER);
define("DB_DEFAULT",DB_MAIN_NAME);
define("DB_LOG",DB_LOG_NAME);

global $DATABASE_INFO;

$DATABASE_INFO = [
    DB_DEFAULT => [
        "DB_DRIVER" => DB_MAIN_DRIVER,
        "DB_HOST" => DB_MAIN_HOST,
        "DB_NAME" => DB_MAIN_NAME,
        "DB_USER" => DB_MAIN_USER,
        "DB_PASSWORD" => DB_MAIN_PASSWORD,
        "DB_PORT" => DB_MAIN_PORT,
		"DB_AUTOCOMMIT" => 0,
		"DB_DEBUG" => 0,
    ],
    DB_LOG => [
        "DB_DRIVER" => DB_LOG_DRIVER,
        "DB_HOST" => DB_LOG_HOST,
        "DB_NAME" => DB_LOG_NAME,
        "DB_USER" => DB_LOG_USER,
        "DB_PASSWORD" => DB_LOG_PASSWORD,
        "DB_PORT" => DB_LOG_PORT,
		"DB_AUTOCOMMIT" => 1,
		"DB_DEBUG" => 0,
    ],
];

System::appReady($url = $URL);

?>