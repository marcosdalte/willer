<?php

namespace Core {
    use \Exception as Exception;
    use \Core\Exception\ExceptionHandler;

    class System {
        public function __construct($url) {
            $this->readyApp($url);
        }

        private function readyApp($url) {
            $this->readyErrorHandler();
            $this->readyUrlRoute($url,REQUEST_URI);
        }

        private function readyErrorHandler() {
            $whoops_run = new \Whoops\Run();
            $whoops_pretty_page_handler = new \Whoops\Handler\PrettyPageHandler();
            $whoops_json_response_handler = new \Whoops\Handler\JsonResponseHandler();

            $whoops_run->pushHandler($whoops_pretty_page_handler);
            $whoops_run->pushHandler(function ($exception,$inspector,$whoops_run) {
                new ExceptionHandler($inspector->getException()->getMessage());

                $inspector->getFrames()->map(function ($frame) {
                    $frame_function = $frame->getFunction();

                    if (!empty($frame_function)) {
                        $frame->addComment($frame_function,'Function');
                    }

                    return $frame;
                });
            });

            $whoops_run->register();

            $whoops_pretty_page_handler->addDataTable('Willer Contants',array(
                'URL_PREFIX' => URL_PREFIX,
                'REQUEST_URI' => REQUEST_URI,
                'DEBUG' => DEBUG,
                'ROOT_PATH' => ROOT_PATH
            ));
        }

        private function urlRoute($application_route,$matche) {
            if (count($application_route) != 2) {
                throw new Exception('applicationFormatError');
            }

            $request_method = $application_route[1];
            $application_route = $application_route[0];

            $application_route = explode('/',$application_route);

            if (count($application_route) < 3) {
                throw new Exception('applicationFormatError');
            }

            $application = $application_route[0];
            $controller = $application_route[1];
            $controller_action = $application_route[2];

            $application = vsprintf('Application\\%s\\Controller\\%s',[$application,$controller]);

            if (!file_exists(ROOT_PATH.'/'.str_replace('\\','/',$application).'.php')) {
                throw new Exception('applicationFileNotFound');
            }

            try {
                $new_application = new $application($request_method);

            } catch (Exception $error) {
                throw new Exception($error);
            }

            if (empty(method_exists($new_application,$controller_action))) {
                throw new Exception('applicationMethodNotFound');
            }

            if (!empty($matche)) {
                array_shift($matche);
            }

            try {
                return $new_application->$controller_action(...$matche);

            } catch (Exception $error) {
                throw new Exception($error);
            }
        }

        private function readyUrlRoute($url,$request_uri) {
            $request_uri = str_replace(URL_PREFIX,'',$request_uri);

            if ($request_uri_strstr = strstr($request_uri,'?',true)) {
                $request_uri = $request_uri_strstr;
            }

            $url_route = null;

            foreach ($url as $url_er => $application_route) {
                if (preg_match($url_er,$request_uri,$matche)) {
                    try {
                        return $this->urlRoute($application_route,$matche);

                    } catch (Exception $error) {
                        throw new Exception($error);
                    }
                }
            }

            if (empty($url_route)) {
                throw new Exception('requestUriNotFound');
            }
        }
    }
}
