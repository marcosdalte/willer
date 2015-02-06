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

        protected function log($error_name,$message = "") {
            $log_error = new LogError(DB_LOG);
            $log_register = new LogRegister(DB_LOG);

            try {
                $log_error = $log_error->get(["name" => $error_name]);

            } catch (Exception $error) {
                $log_error = null;
                $message .= Util::str("%s\n%s",[$error->getMessage(),$error->getTraceAsString()]);
            }

            try {
                $log_register->save([
                    "log_user_id" => Util::get($GLOBALS["AUTH"],"id",null),
                    "log_error_id" => Util::get($log_error,"id",null),
                    "url" => PAGE,
                    "post" => json_encode($_POST),
                    "get" => json_encode($_GET),
                    "message" => $message,
                    "dateadd" => Util::datetimeNow(),
                ]);

            } catch (Exception $error) {
                throw new Exception($error);
            }
        }

        protected function error($error_name,$message = "") {
            $log_error = new LogError(DB_LOG);
            $log_register = new LogRegister(DB_LOG);

            if (!empty($message)) {
                $message .= Util::str("%s\n%s",[$message->getMessage(),$message->getTraceAsString()]);
            }

            try {
                $log_error = $log_error->get(["name" => $error_name]);

            } catch (Exception $error) {
                $message .= Util::str("%s\n%s",[$error->getMessage(),$error->getTraceAsString()]);
                $log_error = null;
            }

            try {
                $log_register->save([
                    "log_user_id" => Util::get($GLOBALS["AUTH"],"id",null),
                    "log_error_id" => Util::get($log_error,"id",null),
                    "url" => PAGE,
                    "post" => json_encode($_POST),
                    "get" => json_encode($_GET),
                    "message" => $message,
                    "dateadd" => Util::datetimeNow(),]);

            } catch (Exception $error) {
                $message .= Util::str("%s\n%s",[$error->getMessage(),$error->getTraceAsString()]);
            }

            return Util::renderToJson([
                "error_name" => Util::get($log_error,"name",null),
                "error_message" => Util::get($log_error,"description",null),
                "error_exception" => $message,
                "success" => false,]);
        }
    }
}