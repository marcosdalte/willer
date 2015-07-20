<?php

namespace Application\Test\Model\Test {
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

    class Product extends Model {
        public $id;
        public $name;
        public $price;

        protected function schema() {
            return [
                'id' => Model::primaryKey(),
                'name' => Model::char(['length' => 40]),
                'price' => Model::float(['length' => 20])];
        }

        protected function name() {
            return 'product';
        }
    }

    class Purchase extends Model {
        public $id;
        public $person_id;
        public $product_id;
        public $quantity;

        protected function schema() {
            return [
                'id' => Model::primaryKey(),
                'person_id' => Model::foreignKey(['table' => new Person,'null' => 0]),
                'product_id' => Model::foreignKey(['table' => new Product,'null' => 0]),
                'quantity' => Model::integer(['length' => 20])];
        }

        protected function name() {
            return 'purchase';
        }
    }
}
