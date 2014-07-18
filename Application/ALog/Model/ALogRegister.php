<?php

namespace Application\Log\Model {
    use \DAO\DataManipulationLanguage;
	use \Application\Log\Model\ALogUser;
	use \Application\Log\Model\ALogError;
    class ALogRegister extends DataManipulationLanguage {
        public $id;
        public $log_user_id;
        public $log_error_id;
        public $url;
        public $post;
        public $get;
        public $mensagem;
        public $datacriacao;

        public function name() {
            return "log_register";
        }

        protected function column() {
            return get_object_vars($this);
        }

        public function primaryKey() {
            return "id";
        }

        public function foreignKey() {
			return [
				"log_user_id" => ["index" => new ALogUser, "null" => true],
				"log_error_id" => ["index" => new ALogError, "null" => true],];
        }
    }
}

?>