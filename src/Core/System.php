<?php

namespace Core {
    use \Core\Exception\WF_applicationFormatError;
    use \Core\Exception\WF_applicationFileNotFound;
    use \Core\Exception\WF_applicationMethodNotFound;
    use \Core\Exception\WF_requestUriNotFound;

    class System {
        public function __construct($url) {
            $this->readyApp($url);
        }

        private function readyApp($url) {
            $this->readyErrorHandler();
            $this->readyUrlRoute($url,REQUEST_URI);
        }

        private function readyErrorHandler() {
            if (empty(DEBUG)) {
                return false;
            }

            $whoops_run = new \Whoops\Run();
            $whoops_pretty_page_handler = new \Whoops\Handler\PrettyPageHandler();
            $whoops_json_response_handler = new \Whoops\Handler\JsonResponseHandler();

            $whoops_json_response_handler->onlyForAjaxRequests(true);

            $whoops_run->pushHandler($whoops_pretty_page_handler);
            $whoops_run->pushHandler(function ($exception,$inspector,$whoops_run) {
                $inspector->getFrames()->map(function ($frame) {
                    $frame_function = $frame->getFunction();

                    if (!empty($frame_function)) {
                        $frame->addComment($frame_function,'Function');
                    }

                    return $frame;
                });
            });

            $whoops_run->pushHandler($whoops_json_response_handler);

            $whoops_run->register();

            $whoops_pretty_page_handler->addDataTable('Willer Contants',array(
                'URL_PREFIX' => URL_PREFIX,
                'REQUEST_URI' => REQUEST_URI,
                'ROOT_PATH' => ROOT_PATH
            ));
        }

        private function urlRoute($application_route,$matche) {
            if (count($application_route) != 2) {
                throw new WF_applicationFormatError(vsprintf('error in list [%s], max of two indices. Ex: [\'Application/Controller/method\',\'(GET|POST|PUT|DELETE)\']',[implode(',',$application_route)]));
            }

            $request_method = $application_route[1];

            if (empty($request_method)) {
                throw new WF_applicationFormatError('WF_applicationFormatError');
            }

            $application_route = $application_route[0];
            $application_route = explode('/',$application_route);

            if (count($application_route) < 3) {
                throw new WF_applicationFormatError('WF_applicationFormatError');
            }

            $application = $application_route[0];
            $controller = $application_route[1];
            $controller_action = $application_route[2];

            $application = vsprintf('Application\\%s\\Controller\\%s',[$application,$controller]);

            if (!file_exists(ROOT_PATH.'/'.str_replace('\\','/',$application).'.php')) {
                throw new WF_applicationFileNotFound('WF_applicationFileNotFound');
            }

            $new_application = new $application($request_method);

            if (empty(method_exists($new_application,$controller_action))) {
                throw new WF_applicationMethodNotFound('WF_applicationMethodNotFound');
            }

            if (!empty($matche)) {
                array_shift($matche);
            }

            return $new_application->$controller_action(...$matche);
        }

        private function readyUrlRoute($url,$request_uri) {
            $request_uri = str_replace(URL_PREFIX,'',$request_uri);

            $request_uri_strstr = strstr($request_uri,'?',true);

            if (!empty($request_uri_strstr)) {
                $request_uri = $request_uri_strstr;
            }

            foreach ($url as $url_er => $application_route) {
                if (preg_match($url_er,$request_uri,$matche)) {
                    return $this->urlRoute($application_route,$matche);
                }
            }

            throw new WF_requestUriNotFound('WF_requestUriNotFound');
        }
    }
}
