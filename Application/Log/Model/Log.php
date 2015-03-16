<?php

namespace Application\Log\Model\Log {
    use \Core\Model;

    class ErrorType extends Model {
        public $id;
        public $name;

        protected function schema() {
            return [
                "id" => Model::primaryKey(),
                "name" => Model::char(["length" => 40])];
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
                "id" => Model::primaryKey(),
                "type_id" => Model::foreignKey(["table" => new ErrorType,"null" => 0]),
                "name" => Model::char(["length" => 40]),
                "describe" => Model::text(["null" => 1])];
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
                "id" => Model::primaryKey([]),
                "active" => Model::integer(["length" => 1]),
                "name" => Model::char(["length" => 30]),
                "publickey" => Model::char(["length" => 40]),
                "dateadd" => Model::datetime([]),
                "dateupdate" => Model::datetime([]),
                "datecancel" => Model::datetime(["null" => 1])];
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
                "id" => Model::primaryKey([]),
                "user_id" => Model::foreignKey(["table" => new User,"null" => 1]),
                "error_id" => Model::foreignKey(["table" => new Error,"null" => 1]),
                "url" => Model::char(["length" => 255]),
                "post" => Model::text(["null" => 1]),
                "get" => Model::text(["null" => 1]),
                "message" => Model::text(["null" => 1]),
                "dateadd" => Model::datetime([]),];
        }

        protected function name() {
            return "register";
        }
    }
}