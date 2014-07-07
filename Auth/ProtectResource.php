<?php

namespace Auth {
    use \Util;
    use \Application\Log\Model\LogUser;
    use \Application\Log\Model\LogRegister;
    use \Application\Log\Model\LogError;
    use \Exception as Exception;
    class ProtectResource {
        public function protect($controller) {
            session_start();

            $request = (object) [
                "POST" => $_POST,
                "GET" => $_GET,
                "COOKIE" => $_COOKIE,
                "SERVER" => $_SERVER,
                "FILES" => $_FILES,
                "SESSION" => $_SESSION,
                "auth" => null,];

            if (PROTECT_RESOURCE) {
                try {
                    $request->auth = $this->auth($request);

                } catch (Exception $error) {
                    return $this->error($request,"auth_key_error",$error);
                }
            }

            $controller = vsprintf("Application\\%s\\Controller\\%s",[APPLICATION,$controller]);
            new $controller($request);
        }

        private function auth($request) {
            $log_user = new LogUser;

            $auth_user = Util::get($request->GET,"auth_user",null);
            $auth_key = Util::get($request->GET,"auth_key",null);

            if (empty($auth_user) || empty($auth_key)) {
                throw new Exception("auth_key_required");

            }

            try {
                $log_user = $log_user->databaseUse(DB_LOG)->get([
                    "nome" => $auth_user,
                    "publickey" => $auth_key,
                    "ativo" => 1]);

            } catch (Exception $error) {
                throw new Exception("auth_key_invalid");
            }

            return $log_user;
        }

        protected function log($request,$error_name,$message = "") {
            $log_error = new LogError;
            $log_register = new LogRegister;

            try {
                $log_error = $log_error->databaseUse(DB_LOG)->get([
                    "nome" => $error_name]);

            } catch (Exception $error) {
                $log_error = null;
                $message .= vsprintf("%s\n%s",[$error->getMessage(),$error->getTraceAsString()]);
            }

            try {
                $log_register->databaseUse(DB_LOG)->save([
                    "log_user_id" => Util::get($request->auth,"id",null),
                    "log_error_id" => Util::get($log_error,"id",null),
                    "url" => PAGE,
                    "post" => json_encode($request->POST),
                    "get" => json_encode($request->GET),
                    "mensagem" => $message,
                    "datacriacao" => date("Y-m-d H:i:s"),
                ]);

            } catch (Exception $error) {
                throw new Exception($error);
            }
        }

        protected function error($request,$error_name,$message = "") {
            $log_error = new LogError;
            $log_register = new LogRegister;

            if (!empty($message)) {
                $message .= vsprintf("%s\n%s",[$message->getMessage(),$message->getTraceAsString()]);
            }

            try {
                $error_get = $log_error->databaseUse(DB_LOG)->get([
                    "nome" => $error_name]);

            } catch (Exception $error) {
                $message .= vsprintf("%s\n%s",$error->getMessage(),$error->getTraceAsString());
                $error_get = null;
            }

            try {
                $log_register->databaseUse(DB_LOG)->save([
                    "log_user_id" => Util::get($request->auth,"id",null),
                    "log_error_id" => Util::get($error_get,"id",null),
                    "url" => PAGE,
                    "post" => json_encode($request->POST),
                    "get" => json_encode($request->GET),
                    "mensagem" => $message,
                    "datacriacao" => date("Y-m-d H:i:s"),]);

            } catch (Exception $error) {
                $message .= vsprintf("%s\n%s",[$error->getMessage(),$error->getTraceAsString()]);
            }

            return Util::renderToJson([
                "error_name" => Util::get($error_get,"nome",null),
                "error_message" => Util::get($error_get,"descricao",null),
                "error_exception" => $message,
                "success" => false,]);
        }
    }
}