<?php

namespace Core {
    use \Exception as Exception;
    use \Core\Log;
    use \Core\Util;

    trait System {
        public static function appReady($url) {
            if (!empty(DEBUG)) {
                ini_set('error_reporting',E_ALL);
                ini_set('display_errors',1);

                Log::write(json_encode([
                    'post' => $_POST,
                    'get' => $_GET,
                    'server' => $_SERVER])."\n",dirname(ROOT_PATH).'/server/log/access_log.txt');
            }

            System::errorHandlerReady();
            System::autoLoadReady();
            System::urlRouteReady($url,REQUEST_URI);
        }

        private static function errorHandlerReady() {
            set_error_handler(function($errno,$errstr,$errfile,$errline,$errcontext) {
                header('Content-Type: application/json');

                $exception = json_encode(array(
    				'message' => $errstr,
    				'file' => $errfile,
                    'line' => $errline
    			));

    			exit($exception);

            });
        }

        private static function autoloadPSR0($path,$file) {
            $path = dirname(ROOT_PATH).'/'.$path;
            $file_path = vsprintf('%s/%s',[$path,$file]);
            $file_path = ltrim($file_path,'\\');
            $directory_separator = '/';
            $file = str_replace('_',$directory_separator,$file_path);

            $file = vsprintf('%s.php',[$file,'.php']);

            return $file;
        }

        private static function autoloadPSR4($path,$file) {
            $path = dirname(ROOT_PATH).'/'.$path;
            $prefix = strstr($file,'/',true);
            $prefix_len = strlen($prefix);
            $relative_class = substr($file,$prefix_len);
            $relative_class = str_replace('\\','/',$relative_class);

            $file = vsprintf('%s%s.php',[$path,$relative_class,'.php']);

            return $file;
        }

        private static function autoLoadReady() {
            spl_autoload_register(function ($file) {
                spl_autoload_unregister(__FUNCTION__);

                $file = str_replace('\\','/',$file);
                $root_path_file = vsprintf('%s/%s.php',[ROOT_PATH,$file]);

                if (file_exists($root_path_file)) {
                    $file = $root_path_file;

                } else  {
                    if (!empty(strpos($root_path_file,'/Model/'))) {
                        $file_explode = explode('/',$root_path_file);

                        array_pop($file_explode);

                        $file = implode('/',$file_explode);
                        $file = vsprintf('%s.php',[$file,]);

                    } else {
                        $vendor_path = Util::loadJsonFile(ROOT_PATH.'/vendor.json',true);

                        if (empty($vendor_path)) {
                            $file = null;

                        } else {
                            foreach ($vendor_path as $path) {
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

                require_once($file);
            });
        }

        private static function urlRoute($application_route,$matche) {
            if (count($application_route) != 2) {
                Util::exceptionToJson(new Exception('application format error'));
            }

            $request_method = $application_route[1];
            $application_route = $application_route[0];

            $application_route = explode('/',$application_route);

            if (count($application_route) < 3) {
                Util::exceptionToJson(new Exception('application format error'));
            }

            $application = $application_route[0];
            $controller = $application_route[1];
            $controller_action = $application_route[2];

            $application = vsprintf('Application\\%s\\Controller\\%s',[$application,$controller]);

            if (!file_exists(ROOT_PATH.'/'.str_replace('\\','/',$application).'.php')) {
                Util::exceptionToJson(new Exception('file not found'));
            }

            try {
                $new_application = new $application($request_method);

            } catch (Exception $error) {
                Util::exceptionToJson($error);
            }

            if (empty(method_exists($new_application,$controller_action))) {
                Util::exceptionToJson(new Exception('method does not exist in object'));
            }

            if (!empty($matche)) {
                array_shift($matche);

            }

            try {
                $new_application->$controller_action(...$matche);

            } catch (Exception $error) {
                Util::exceptionToJson($error);
            }

            return true;
        }

        private static function urlRouteReady($url,$request_uri) {
            $request_uri = preg_replace('/^(\/{1})(.*)/','$2',$request_uri);

            foreach ($url as $url_er => $application_route) {
                if (preg_match($url_er,$request_uri,$matche)) {
                    try {
                        $url_route = System::urlRoute($application_route,$matche);

                    } catch (Exception $error) {
                        Util::exceptionToJson($error);
                    }

                    break;
                }
            }

            if (empty($url_route)) {
                Util::exceptionToJson(new Exception('request uri not found'));
            }
        }
    }
}
