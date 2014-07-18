<?php

namespace Auth {
    use \Util;
    use \Application\Log\Model\ALogUser;
    use \Application\Log\Model\ALogRegister;
    use \Application\Log\Model\ALogError;
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
            $a_log_user = new ALogUser;

            $auth_user = Util::get($request->GET,"auth_user",null);
            $auth_key = Util::get($request->GET,"auth_key",null);

            if (empty($auth_user) || empty($auth_key)) {
                throw new Exception("auth_key_required");

            }

            try {
                $a_log_user = $a_log_user->databaseUse(DB_LOG)->get([
                    "nome" => $auth_user,
                    "publickey" => $auth_key,
                    "ativo" => 1]);

            } catch (Exception $error) {
                throw new Exception("auth_key_invalid");
            }

            return $a_log_user;
        }

        protected function log($request,$error_name,$message = "") {
            $a_log_error = new ALogError;
            $a_log_register = new ALogRegister;

            try {
                $a_log_error = $a_log_error->databaseUse(DB_LOG)->get([
                    "nome" => $error_name]);

            } catch (Exception $error) {
                $log_error = null;
                $message .= vsprintf("%s\n%s",[$error->getMessage(),$error->getTraceAsString()]);
            }

            try {
                $a_log_register->databaseUse(DB_LOG)->save([
                    "log_user_id" => Util::get($request->auth,"id",null),
                    "log_error_id" => Util::get($a_log_error,"id",null),
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
            $a_log_error = new ALogError;
            $a_log_register = new ALogRegister;

            if (!empty($message)) {
                $message .= vsprintf("%s\n%s",[$message->getMessage(),$message->getTraceAsString()]);
            }

            try {
                $a_log_error = $a_log_error->databaseUse(DB_LOG)->get([
                    "nome" => $error_name]);

            } catch (Exception $error) {
                $message .= vsprintf("%s\n%s",$error->getMessage(),$error->getTraceAsString());
                $a_log_error = null;
            }

            try {
                $log_register->databaseUse(DB_LOG)->save([
                    "log_user_id" => Util::get($request->auth,"id",null),
                    "log_error_id" => Util::get($a_log_error,"id",null),
                    "url" => PAGE,
                    "post" => json_encode($request->POST),
                    "get" => json_encode($request->GET),
                    "mensagem" => $message,
                    "datacriacao" => date("Y-m-d H:i:s"),]);

            } catch (Exception $error) {
                $message .= vsprintf("%s\n%s",[$error->getMessage(),$error->getTraceAsString()]);
            }

            return Util::renderToJson([
                "error_name" => Util::get($a_log_error,"nome",null),
                "error_message" => Util::get($a_log_error,"descricao",null),
                "error_exception" => $message,
                "success" => false,]);
        }
    }
}