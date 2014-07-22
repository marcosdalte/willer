<?php

namespace Application\ALog\Model {
    use \DAO\DataManipulationLanguage;
    class ALogUser extends DataManipulationLanguage {
        public $id;
        public $ativo;
        public $nome;
        public $publickey;
        public $datacriacao;
        public $dataatualizacao;
        public $dataexclusao;

        public function name() {
            return "log_user";
        }

        protected function column() {
            return get_object_vars($this);
        }

        public function primaryKey() {
            return "id";
        }

		public function foreignKey() {
			return [];
		}
    }
}

?>