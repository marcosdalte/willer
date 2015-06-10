<?php

namespace Application\Test\Model\Product {
    use \Core\Model;

    class Product extends Model {
        public $id;
        public $name;
        public $price;

        protected function schema() {
            return [
                "id" => Model::primaryKey(),
                "name" => Model::char(["length" => 40]),
                "price" => Model::float(["length" => 20])];
        }

        protected function name() {
            return "product";
        }
    }
}
