<?php

namespace Application\Test\Model\Person {
    use \Core\Model;

    class Person extends Model {
        public $id;
        public $first_name;
        public $last_name;

        protected function schema() {
            return [
                'id' => Model::primaryKey(),
                'first_name' => Model::char(['length' => 40]),
                'last_name' => Model::char(['length' => 40])];
        }

        protected function name() {
            return 'person';
        }
    }
}
