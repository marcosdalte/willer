<?php

namespace Application\ALog\Model {
	use \Helper\Model;

	class LogError extends Model {
		public $id;
		public $name;
		public $describe;

		protected function schema() {
			return [
				"id" => $this->primaryKeyField([]),
				"name" => $this->charField(["length" => 40]),
				"describe" => $this->textField(["blank" => 1,"null" => 1])];
		}

		protected function name() {
			return "log_error";
		}
	}
}

?>