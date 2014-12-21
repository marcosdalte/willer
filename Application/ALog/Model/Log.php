<?php

namespace Application\ALog\Model\Log {
	use \Core\Model;

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

	class LogUser extends Model {
        public $id;
        public $active;
        public $name;
        public $publickey;
        public $dateadd;
        public $dateupdate;
        public $datecancel;

		protected function schema() {
			return [
				"id" => $this->primaryKeyField([]),
				"active" => $this->integerField(["length" => 1]),
				"name" => $this->charField(["length" => 30]),
				"publickey" => $this->charField(["length" => 40]),
				"dateadd" => $this->datetimeField([]),
				"dateupdate" => $this->datetimeField([]),
				"datecancel" => $this->datetimeField(["null" => 1])];
		}

		protected function name() {
            return "log_user";
        }
    }

	class LogRegister extends Model {
        public $id;
        public $log_user_id;
        public $log_error_id;
        public $url;
        public $post;
        public $get;
        public $message;
        public $dateadd;

		protected function schema() {
			return [
				"id" => $this->primaryKeyField([]),
				"log_user_id" => $this->foreignKeyField(["table" => new LogUser, "null" => 1]),
				"log_error_id" => $this->foreignKeyField(["table" => new LogError, "null" => 1]),
				"url" => $this->charField(["length" => 255]),
				"post" => $this->textField(["null" => 1]),
				"get" => $this->textField(["null" => 1]),
				"message" => $this->textField(["null" => 1]),
				"dateadd" => $this->datetimeField([]),];
		}

		protected function name() {
            return "log_register";
        }
    }
}

?>