<?php

namespace Application\ALog\Model\Log {
	use \Core\Model;

	class ErrorType extends Model {
		public $id;
		public $name;

		protected function schema() {
			return [
				"id" => $this->primaryKey(),
				"name" => $this->char(["length" => 40])];
		}

		protected function name() {
			return "errortype";
		}
	}

	class Error extends Model {
		public $id;
		public $type_id;
		public $name;
		public $describe;

		protected function schema() {
			return [
				"id" => $this->primaryKey(),
				"type_id" => $this->foreignKey(["table" => new ErrorType,"null" => 0]),
				"name" => $this->char(["length" => 40]),
				"describe" => $this->text(["null" => 1])];
		}

		protected function name() {
			return "error";
		}
	}

	class User extends Model {
        public $id;
        public $active;
        public $name;
        public $publickey;
        public $dateadd;
        public $dateupdate;
        public $datecancel;

		protected function schema() {
			return [
				"id" => $this->primaryKey([]),
				"active" => $this->integer(["length" => 1]),
				"name" => $this->char(["length" => 30]),
				"publickey" => $this->char(["length" => 40]),
				"dateadd" => $this->datetime([]),
				"dateupdate" => $this->datetime([]),
				"datecancel" => $this->datetime(["null" => 1])];
		}

		protected function name() {
            return "user";
        }
    }

	class Register extends Model {
        public $id;
        public $user_id;
        public $error_id;
        public $url;
        public $post;
        public $get;
        public $message;
        public $dateadd;

		protected function schema() {
			return [
				"id" => $this->primaryKey([]),
				"user_id" => $this->foreignKey(["table" => new User,"null" => 1]),
				"error_id" => $this->foreignKey(["table" => new Error,"null" => 1]),
				"url" => $this->char(["length" => 255]),
				"post" => $this->text(["null" => 1]),
				"get" => $this->text(["null" => 1]),
				"message" => $this->text(["null" => 1]),
				"dateadd" => $this->datetime([]),];
		}

		protected function name() {
            return "register";
        }
    }
}