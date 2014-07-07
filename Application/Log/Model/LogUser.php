<?php

namespace Application\Log\Model {
    use \DAO\DataManipulationLanguage;
    class LogUser extends DataManipulationLanguage {
        public $id;
        public $ativo;
        public $nome;
        public $publickey;
        public $datacriacao;
        public $dataatualizacao;
        public $dataexclusao;
        protected function name() {
            return "log_user";
        }
        protected function column() {
            return get_object_vars($this);
        }
        protected function primaryKey() {
            return ["id"];
        }
        protected function api_usuario_related() {
            return $this->related([
                "log_register" => [
                    "user_id" => ["log_user" => "id"]],
                "log_error" => [
                    "id" => ["log_register" => "error_id"]]
                ]);
        }
    }
}

?>