<?php

namespace Application\Log\Model\ErrorType {
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
}