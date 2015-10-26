<?php

namespace Core {
    use \Exception as Exception;
    use \Core\Util;

    abstract class Controller {
        public function __construct($request_method = null) {
            try {
                $this->requestMethodAccess($request_method);

            } catch (Exception $error) {
                throw new Exception($error);
            }
        }

        private function requestMethodAccess($request_method = null) {
            if (empty(Util::get($_SERVER,'REQUEST_METHOD',null))) {
                throw new Exception('WF_serverRequestMethodEmpty');
            }

            if (!empty($request_method)) {
                if (is_array($request_method)) {
                    if (!in_array($_SERVER['REQUEST_METHOD'],$request_method)) {
                        throw new Exception('WF_requestMethodInvalid');
                    }

                } else {
                    if ($_SERVER['REQUEST_METHOD'] != $request_method) {
                        throw new Exception('WF_requestMethodInvalid');
                    }
                }
            }
        }
    }
}
