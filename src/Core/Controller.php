<?php

namespace Core {
    use \Exception as Exception;

    abstract class Controller {
        public function __construct($request_method = null) {
            try {
                $this->requestMethodAccess($request_method);

            } catch (Exception $error) {
                throw new Exception($error);
            }
        }

        private function requestMethodAccess($request_method = null) {
            if (!empty($request_method)) {
                if (is_array($request_method)) {
                    if (!in_array($_SERVER["REQUEST_METHOD"],$request_method)) {
                        throw new Exception("request method invalid");
                    }

                } else {
                    if ($_SERVER["REQUEST_METHOD"] != $request_method) {
                        throw new Exception("request method invalid");
                    }
                }
            }
        }
    }
}
