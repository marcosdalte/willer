<?php

namespace Core {
    use \Exception as Exception;
    use \Core\Util;

    trait System {
        public static function appReady($url = []) {
            System::errorHandler();
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

        private static function iniSetReady() {
            date_default_timezone_set(TIMEZONE);
            ini_set("error_reporting",ERROR_REPORTING);
            ini_set("display_errors",DISPLAY_ERRORS);
        }

        private static function autoLoadReady() {
            spl_autoload_register(function ($file) {
                spl_autoload_unregister(__FUNCTION__);

                $file = str_replace("\\","/",$file);
                $file = vsprintf("%s/%s.php",[ROOT_PATH,$file]);

                if (!file_exists($file)) {
                    if (!empty(strpos($file,"/Model/"))) {
                        $file_explode = explode("/",$file);

                        array_pop($file_explode);

                        $file = implode("/",$file_explode);
                        $file = vsprintf("%s.php",[$file,]);

                    } else {
                        $lib_path = LIB_PATH;

                        if (empty($lib_path)) {
                            $file = null;

                        } else {
                            $file_explode = explode("/",$file);
                            $file = array_pop($file_explode);
                            $file = str_replace("_","/",$file);

                            foreach ($lib_path as $path) {
                                $file = Util::str("%s/%s",[$path,$file]);

                                if (file_exists($file)) {
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

        private static function urlRouteReady($url,$http_path) {
            $flag = false;

            foreach ($url as $url_er => $application) {
                if (preg_match($url_er,$http_path,$matche)) {
                    $flag = true;
                    $request_method = null;

                    $controller = explode("/",$application[0]);

                    if (!empty($application[1])) {
                        $request_method = $application[1];
                    }

                    $application = Util::str("Application\\%s\\Controller\\%s",[$controller[0],$controller[1]]);
                    $controller_action = $controller[2];

                    try {
                        $new_application = new $application($request_method);

                    } catch (Exception $error) {
                        Util::exceptionToJson($error);
                    }

                    if (empty(method_exists($new_application,$controller_action))) {
                        Util::exceptionToJson(new Exception("method does not exist in object"));
                    }

                    unset($matche[0]);

                    try {
                        $new_application->$controller_action(...$matche);

                    } catch (Exception $error) {
                        Util::exceptionToJson($error);
                    }

                    break;
                }
            }

            if (empty($flag)) {
                Util::httpRedirect(URL_NOT_FOUND);
            }
        }
    }
}
