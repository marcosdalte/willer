<?php

namespace Application\Log\Model {
	use \DAO\DataManipulationLanguage;
	class ALogError extends DataManipulationLanguage {
		public $id;
		public $nome;
		public $descricao;

		public function name() {
			return "log_error";
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