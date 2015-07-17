<?php

namespace Core {
    use \Exception as Exception;
    use \Core\Util;

    trait System {
        public static function appReady($url = []) {

            define("DISPLAY_ERRORS",1);
            define("ERROR_REPORTING","E_ALL");
            define("TIMEZONE","America/Sao_Paulo");
            // define("HTTP_TYPE","http");
            // define("PUBLIC_PATH",Util::str("%s://%s/public",[HTTP_TYPE,URL_SITE]));
            // define("URL_BASE",Util::str("%s://%s",[HTTP_TYPE,URL_SITE]));
            // define("URL_NOT_FOUND",Util::str("%s/404.html",[URL_BASE,]));
            // define("QUERY_LIMIT_ROW",15);
            // define("SESSION_LIMIT",10);
            // define("DB_DEFAULT","db_default");
            //
            // const LIB_PATH = [
            //     "../vendor/twig/twig/lib",
            // ];
            //
            // const DATABASE_INFO = [
            //     DB_DEFAULT => [
            //         "DB_DRIVER" => DB_DEFAULT_DRIVER,
            //         "DB_HOST" => DB_DEFAULT_HOST,
            //         "DB_NAME" => DB_DEFAULT_NAME,
            //         "DB_USER" => DB_DEFAULT_USER,
            //         "DB_PASSWORD" => DB_DEFAULT_PASSWORD,
            //         "DB_PORT" => DB_DEFAULT_PORT,
            //         "DB_AUTOCOMMIT" => 0,
            //         "DB_DEBUG" => 0,
            //     ],
            // ];

            System::errorHandler();
            System::loadConfig();
            System::iniSetReady();
            System::autoLoadReady();
            System::sessionReady();
            System::urlRouteReady($url,HTTP_PATH);
        }

        private static function errorHandler() {
            set_error_handler(function($errno,$errstr,$errfile,$errline,$errcontext) {
                header("Content-Type: application/json");

                $exception = json_encode(array(
    				"message" => $errstr,
    				"file" => $errfile,
                    "line" => $errline
    			));

    			exit($exception);

            });
        }

        private static function loadConfig() {
            define("HTTP_PATH",Util::get($_SERVER,"REQUEST_URI",null));
            define("ROOT_PATH",__DIR__);

            $config_yaml = yaml_parse_file(ROOT_PATH."/config.php");

            print_r($config_yaml);
            exit();
        }

        private static function iniSetReady() {
            date_default_timezone_set(TIMEZONE);
            ini_set("error_reporting",ERROR_REPORTING);
            ini_set("display_errors",DISPLAY_ERRORS);
        }

        private static function autoloadPSR0($path,$file) {
            $file_path = vsprintf("%s/%s",[$path,$file]);
            $file_path = ltrim($file_path,"\\");
            $directory_separator = "/";
            $file = str_replace("_",$directory_separator,$file_path);

            $file = vsprintf("%s.php",[$file,".php"]);

            return $file;
        }

        private static function autoloadPSR4($path,$file) {
            $prefix = strstr($file,"/",true);
            $len = strlen($prefix);
            $relative_class = substr($file,$len);
            $relative_class = str_replace("\\","/",$relative_class);

            $file = vsprintf("%s%s.php",[$path,$relative_class,".php"]);

            return $file;
        }

        private static function autoLoadReady() {
            spl_autoload_register(function ($file) {
                spl_autoload_unregister(__FUNCTION__);

                $file = str_replace("\\","/",$file);
                $root_path_file = vsprintf("%s/%s.php",[ROOT_PATH,$file]);

                if (file_exists($root_path_file)) {
                    $file = $root_path_file;

                } else  {
                    if (!empty(strpos($root_path_file,"/Model/"))) {
                        $file_explode = explode("/",$root_path_file);

                        array_pop($file_explode);

                        $file = implode("/",$file_explode);
                        $file = vsprintf("%s.php",[$file,]);

                    } else {
                        $lib_path = LIB_PATH;

                        if (empty($lib_path)) {
                            $file = null;

                        } else {
                            foreach ($lib_path as $path) {
                                $file_path = System::autoloadPSR0($path,$file);

                                if (file_exists($file_path)) {
                                    $file = $file_path;

                                    break;
                                }

                                $file_path = System::autoloadPSR4($path,$file);

                                if (file_exists($file_path)) {
                                    $file = $file_path;

                                    break;
                                }
                            }
                        }
                    }
                }

                include_once $file;
            });
        }

        private static function sessionReady() {
            session_start();
        }

        private static function urlRoute($application_route,$matche,$flag_url_core) {
            $application_route = explode("/",$application_route);

            if (count($application_route) != 3) {
                if (!empty($flag_url_core)) {
                    return false;
                }

                Util::exceptionToJson(new Exception("application format error"));
            }

            $application = $application_route[0];
            $controller = $application_route[1];
            $controller_action = $application_route[2];

            $application = Util::str("Application\\%s\\Controller\\%s",[$application,$controller]);

            if (!file_exists(ROOT_PATH."/".str_replace("\\","/",$application).".php")) {
                if (!empty($flag_url_core)) {
                    return false;
                }

                Util::exceptionToJson(new Exception("file not found"));
            }

            try {
                $new_application = new $application();

            } catch (Exception $error) {
                Util::exceptionToJson($error);
            }

            if (empty(method_exists($new_application,$controller_action))) {
                Util::exceptionToJson(new Exception("method does not exist in object"));
            }

            if (!empty($matche)) {
                unset($matche[0]);

            }

            try {
                $new_application->$controller_action(...$matche);

            } catch (Exception $error) {
                Util::exceptionToJson($error);
            }

            return true;
        }

        private static function urlRouteReady($url,$http_path) {
            $http_path = preg_replace("/^(\/{1})(.*)/","$2",$http_path);

            try {
                $url_route = System::urlRoute($http_path,[],true);

            } catch (Exception $error) {
                Util::exceptionToJson($error);
            }

            if (!empty($url_route)) {
                return true;
            }

            foreach ($url as $url_er => $application_route) {
                if (preg_match($url_er,$http_path,$matche)) {
                    try {
                        $url_route = System::urlRoute($application_route,$matche,false);

                    } catch (Exception $error) {
                        Util::exceptionToJson($error);
                    }

                    break;
                }
            }

            if (empty($url_route)) {
                Util::httpRedirect(URL_NOT_FOUND);
            }
        }
    }
}
