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
define("DB_MYSQL","db_mysql");
define("DB_SQLITE","db_sqlite");

const DATABASE_INFO = [
    DB_DEFAULT => [
        "DB_DRIVER" => DB_PGSQL_DRIVER,
        "DB_HOST" => DB_PGSQL_HOST,
        "DB_NAME" => DB_PGSQL_NAME,
        "DB_USER" => DB_PGSQL_USER,
        "DB_PASSWORD" => DB_PGSQL_PASSWORD,
        "DB_PORT" => DB_PGSQL_PORT,
		"DB_AUTOCOMMIT" => 0,
		"DB_DEBUG" => 0,
    ],
    DB_MYSQL => [
        "DB_DRIVER" => DB_MYSQL_DRIVER,
        "DB_HOST" => DB_MYSQL_HOST,
        "DB_NAME" => DB_MYSQL_NAME,
        "DB_USER" => DB_MYSQL_USER,
        "DB_PASSWORD" => DB_MYSQL_PASSWORD,
        "DB_PORT" => DB_MYSQL_PORT,
        "DB_AUTOCOMMIT" => 0,
        "DB_DEBUG" => 0,
    ],
    DB_SQLITE => [
        "DB_DRIVER" => DB_SQLITE_DRIVER,
        "DB_HOST" => DB_SQLITE_HOST,
        "DB_NAME" => DB_SQLITE_NAME,
        "DB_USER" => DB_SQLITE_USER,
        "DB_PASSWORD" => DB_SQLITE_PASSWORD,
        "DB_PORT" => DB_SQLITE_PORT,
        "DB_AUTOCOMMIT" => 0,
        "DB_DEBUG" => 0,
    ],
];

System::appReady($URL);
