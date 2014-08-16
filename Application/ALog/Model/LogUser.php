<?php

namespace Application\ALog\Model {
    use \Helper\Model;

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
}

?>