<?php

namespace Application\Test\Model\Test {
    use \Core\Model;
    use \Application\Test\Model\SubTest;

    class Test extends Model {
        public $id;
        public $first_name;
        public $last_name;

        protected function schema() {
            return [
                "id" => Model::primaryKey(),
                "first_name" => Model::char(["length" => 40]),
                "last_name" => Model::char(["length" => 40]),];
        }

        protected function name() {
            return "test";
        }

        public function oneToMany() {

        }
    }
}
