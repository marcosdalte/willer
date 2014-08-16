<?php

namespace Application\ALog\Model {
	use \Helper\Model;
	use \Application\ALog\Model\LogUser;
	use \Application\ALog\Model\LogError;

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
				"log_user_id" => $this->foreignKeyField(["index" => new LogUser, "null" => 1]),
				"log_error_id" => $this->foreignKeyField(["index" => new LogError, "null" => 1]),
				"active" => $this->integerField(["length" => 1]),
				"name" => $this->charField(["length" => 30]),
				"publickey" => $this->charField(["length" => 40]),
				"dateadd" => $this->datetimeField([]),
				"dateupdate" => $this->datetimeField([]),
				"datecancel" => $this->datetimeField(["null" => 1])];
		}

		protected function name() {
            return "log_register";
        }
    }
}

?>