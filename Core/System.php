<?php

namespace Core {
    use \Exception as Exception;
    use \Core\Util;

    trait System {
        public static function appReady($url = []) {
            define("REQUEST_METHOD",$_SERVER["REQUEST_METHOD"]);

            System::iniSetReady();
            System::autoLoadReady();
            System::sessionReady();
            System::urlRouteReady($url,HTTP_PATH);
        }

        private static function iniSetReady() {
            date_default_timezone_set(TIMEZONE);
            ini_set("error_reporting",ERROR_REPORTING);
            ini_set("display_errors",DISPLAY_ERRORS);
        }

        private static function autoLoadReady() {
            spl_autoload_register(function ($class) {
                $class_ = $class;
                $flag_class_exist = false;

                $class = str_replace("\\","/",$class);
                $class = vsprintf("%s/%s.php",[ROOT_PATH,$class]);

                if (!file_exists($class)) {
                    if (strpos($class,"/Model/") !== false) {
                        $class_explode = explode("/",$class);

                        unset($class_explode[count($class_explode) - 1]);

                        $class = implode("/",$class_explode);
                        $class = vsprintf("%s.php",[$class,]);

                    } else {
                        $scan_dir = array_diff(scandir(ROOT_PATH."/Vendor"),array("..","."));

                        if (empty($scan_dir)) {
                            $class = null;

                        } else {
                            foreach ($scan_dir as $dir) {
                                $class = Util::str("%s/Vendor/%s/%s.php",[ROOT_PATH,$dir,$class_]);
                                $class = str_replace("\\","/",$class);

                                if (file_exists($class)) {
                                    break;
                                }

                                $class = null;
                            }
                        }
                    }
                }

                if (empty($class)) {
                    Util::httpRedirect(URL_NOT_FOUND);

                }

                include_once $class;
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

                    try {
                        $new_application->$controller_action($matche);

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