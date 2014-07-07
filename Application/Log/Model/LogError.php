<?php

namespace Application\Log\Model {
	use \DAO\DataManipulationLanguage;
	class LogError extends DataManipulationLanguage {
		public $id;
		public $nome;
		public $descricao;
		protected function name() {
			return "log_error";
		}
		protected function column() {
            return get_object_vars($this);
        }
		protected function primaryKey() {
			return ["id"];
		}
	}
}

?>