<?php

namespace Application\Log\Model {
    use \DAO\DataManipulationLanguage;
    class LogRegister extends DataManipulationLanguage {
        public $id;
        public $log_user_id;
        public $log_error_id;
        public $url;
        public $post;
        public $get;
        public $mensagem;
        public $datacriacao;
        protected function name() {
            return "log_register";
        }
        protected function column() {
            return get_object_vars($this);
        }
        protected function primaryKey() {
            return [
                "id",
                "log_user_id",
                "log_error_id"];
        }
        protected function api_log_related() {
            return $this->related([
                "log_user" => [
                    "id" => ["log_register" => "user_id"]],
                "log_error" => [
                    "id" => ["log_register" => "error_id"]]
                ]);
        }
    }
}

?>