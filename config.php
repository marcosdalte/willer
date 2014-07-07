<?php

include_once("define.php");
include_once("Util.php");
include_once("url.php");

define("PAGE",Util::get($_GET,"page",null));
define("VERSION","0.0.1");
define("DISPLAY_ERRORS",1);
define("ERROR_REPORTING",E_ALL);
define("TIMEZONE","America/Sao_Paulo");
define("ROOT_PATH",__DIR__);
define("HTTP_TYPE","http");
define("PUBLIC_PATH",vsprintf("%s://%s/public",[HTTP_TYPE,URL_SITE]));
define("TEMPLATE_PATH",vsprintf("%s://%s/public/template",[HTTP_TYPE,URL_SITE]));
define("URL_BASE",vsprintf("%s://%s",[HTTP_TYPE,URL_SITE]));
define("DB_DEFAULT",DB_MAIN_NAME);
define("DB_LOG",DB_LOG_NAME);
define("DB_AUTOCOMMIT",0);

date_default_timezone_set(TIMEZONE);
ini_set("error_reporting",ERROR_REPORTING);
ini_set("display_errors",DISPLAY_ERRORS);

$database_info = [
    DB_DEFAULT => [
        "DB_DRIVER" => DB_MAIN_DRIVER,
        "DB_HOST" => DB_MAIN_HOST,
        "DB_NAME" => DB_MAIN_NAME,
        "DB_USER" => DB_MAIN_USER,
        "DB_PASSWORD" => DB_MAIN_PASSWORD,
        "DB_PORT" => DB_MAIN_PORT,
    ],
    DB_LOG => [
        "DB_DRIVER" => DB_LOG_DRIVER,
        "DB_HOST" => DB_LOG_HOST,
        "DB_NAME" => DB_LOG_NAME,
        "DB_USER" => DB_LOG_USER,
        "DB_PASSWORD" => DB_LOG_PASSWORD,
        "DB_PORT" => DB_LOG_PORT,
    ],
];

Util::autoLoad();

$url_route = Util::urlRoute($URL,PAGE);

if (empty($url_route)) {
    exit();
}

define("APPLICATION",$url_route["application"]);
define("PROTECT_RESOURCE",$url_route["protect_resource"]);
define("CONTROLLER",$url_route["controller"]);

$rain_tpl_configure = [
    "tpl_dir" => vsprintf("%s/Application/%s/View/",[ROOT_PATH,APPLICATION]),
    "cache_dir" => vsprintf("%s/Application/%s/View/cache/",[ROOT_PATH,APPLICATION]),
    "path_replace" => false,
    "check_template_update" => true,
];

$protect_resource = "Auth\\ProtectResource";
$protect_resource = new $protect_resource();
$protect_resource->protect(CONTROLLER);

?>