<?php

namespace Application\Test\Model\Person {
    use \Core\Model;

    class Person extends Model {
        public $id;
        public $name;

        protected function schema() {
            return [
                "id" => Model::primaryKey(),
                "name" => Model::char(["length" => 40])];
        }

        protected function name() {
            return "person";
        }
    }
}
