<?php

namespace Core {
	use \Exception as Exception;
    use \Core\Util;
    use \Application\ALog\Model\Log;

    abstract class Controller {
        public function __construct() {
            try {
                $this->apiRestAccess(REST_RULE);
				$this->protectResource($_GET);

            } catch (Exception $error) {
				throw new Exception($this->error($error));
            }
        }

        private function apiRestAccess($method = null) {
            if (empty($method)) {
                return false;
            }
        }

        private function protectResource($get = null) {
            if (empty(PROTECT_RESOURCE)) {
                return false;
            }

			try {
            	$log_user = new LogUser(DB_LOG);

			} catch (Exception $error) {
				throw new Exception($error);
			}

            $auth_user = Util::get($_GET,"auth_user",null);
            $auth_key = Util::get($_GET,"auth_key",null);

            if (empty($auth_user) || empty($auth_key)) {
                throw new Exception("auth_key_required");

            }

            try {
                $log_user = $log_user->get(["name" => $auth_user,"publickey" => $auth_key,"active" => 1]);

            } catch (Exception $error) {
                throw new Exception($error);
            }

            $GLOBALS["AUTH"] = $log_user;

            return $log_user;
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