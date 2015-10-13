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

                file_put_contents(dirname(ROOT_PATH).'/server/log/access_log.txt',json_encode(['post' => $_POST,'get' => $_GET,'server' => $_SERVER])."\n",FILE_APPEND);
            }

            System::errorHandlerReady();
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

                print $exception;

                exit();
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
                return $new_application->$controller_action(...$matche);

            } catch (Exception $error) {
                Util::exceptionToJson($error);
            }
        }

        private static function urlRouteReady($url,$request_uri) {
            $request_uri = preg_replace('/^(\/{1})(.*)/','$2',$request_uri);

            if ($request_uri_strstr = strstr($request_uri,'?',true)) {
                $request_uri = $request_uri_strstr;
            }

            $url_route = null;

            foreach ($url as $url_er => $application_route) {
                if (preg_match($url_er,$request_uri,$matche)) {
                    try {
                        return System::urlRoute($application_route,$matche);

                    } catch (Exception $error) {
                        Util::exceptionToJson($error);
                    }
                }
            }

            if (empty($url_route)) {
                Util::exceptionToJson(new Exception('request uri not found'));
            }
        }
    }
}
