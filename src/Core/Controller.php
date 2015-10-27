<?php

namespace Core {
    use \Core\Util;
    use \Core\Exception\WF_serverRequestMethodEmpty;
    use \Core\Exception\WF_requestMethodInvalid;

    abstract class Controller {
        public function __construct($request_method = null) {
            $this->requestMethodAccess($request_method);
        }

        private function requestMethodAccess($request_method = null) {
            if (empty(Util::get($_SERVER,'REQUEST_METHOD',null))) {
                throw new WF_serverRequestMethodEmpty('REQUEST_METHOD is empty');
            }

            if (!empty($request_method)) {
                if (is_array($request_method)) {
                    if (!in_array($_SERVER['REQUEST_METHOD'],$request_method)) {
                        throw new WF_requestMethodInvalid(vsprintf('REQUEST_METHOD(%s) is different %s',[$_SERVER['REQUEST_METHOD'],implode(',',$request_method)]));
                    }

                } else {
                    if ($_SERVER['REQUEST_METHOD'] != $request_method) {
                        throw new WF_requestMethodInvalid(vsprintf('REQUEST_METHOD(%s) is different %s',[$_SERVER['REQUEST_METHOD'],$request_method]));
                    }
                }
            }
        }
    }
}
