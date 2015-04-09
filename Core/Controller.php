<?php

namespace Core {
	use \Exception as Exception;
    use \Core\Util;
    use \Application\ALog\Model\Log;

    abstract class Controller {
        public function __construct($request_method = null) {
            try {
                $this->apiRestAccess($request_method);

            } catch (Exception $error) {
				throw new Exception($error);
            }
        }

        private function apiRestAccess($request_method = null) {
            if (!empty($request_method)) {
                if (is_array($request_method)) {
                    if (!in_array(REQUEST_METHOD,$request_method)) {
                        throw new Exception("request method invalid");
                    }

                } else {
                    if (REQUEST_METHOD != $request_method) {
                        throw new Exception("request method invalid");
                    }
                }
            }
        }
    }
}