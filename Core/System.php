<?php

namespace Core {
    use \Exception as Exception;
    use \Core\Util;
    use \Core\DAO\Transaction;

    trait System {
        public static function appReady($URL) {
            global $AUTH;
            global $TRANSACTION;

            System::iniSetReady();
            System::autoLoadReady();
            System::sessionReady();
            System::urlRouteReady($URL,HTTP_PATH);
        }

        private static function iniSetReady() {
            date_default_timezone_set(TIMEZONE);
            ini_set("error_reporting",ERROR_REPORTING);
            ini_set("display_errors",DISPLAY_ERRORS);
        }

        private static function autoLoadReady() {
            spl_autoload_register(function ($class) {
                $class_ = $class;

                $class = str_replace("\\","/",$class);
                $class = vsprintf("%s/%s.php",[ROOT_PATH,$class]);

                if (!file_exists($class)) {
                    if (strpos($class,"/Model/") !== false) {
                        $class_explode = explode("/",$class);

                        unset($class_explode[count($class_explode) - 1]);

                        $class = implode("/",$class_explode);
                        $class = vsprintf("%s.php",[$class]);

                    } else {
                        $scan_dir = array_diff(scandir(ROOT_PATH."/Vendor"),array("..","."));

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
                if (preg_match($url_er,$http_path)) {
                    $flag = true;

                    $controller = explode("\\",$application["controller"]);

                    define("APPLICATION",$controller[0]);
                    define("CONTROLLER",$controller[1]);
                    define("PROTECT_RESOURCE",$application["protect_resource"]);

                    $controller = Util::str("Application\\%s\\Controller\\%s",[APPLICATION,CONTROLLER]);

                    try {
                        new $controller();

                    } catch (Exception $error) {
                        Util::renderToJson($error);
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

?>
