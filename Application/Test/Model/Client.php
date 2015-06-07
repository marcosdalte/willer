<?php

namespace Application\Test\Model\Client {
    use \Core\Model;

    class Client extends Model {
        public $id;
        public $name;

        protected function schema() {
            return [
                "id" => Model::primaryKey(),
                "name" => Model::char(["length" => 40])];
        }

        protected function name() {
            return "client";
        }
    }
}
