<?php

namespace Application\Test\Model\Purchase {
    use \Core\Model;
    use \Application\Test\Model\Person;
    use \Application\Test\Model\Product;

    class Purchase extends Model {
        public $id;
        public $person_id;
        public $product_id;
        public $quantity;

        protected function schema() {
            return [
                "id" => Model::primaryKey(),
                "person_id" => Model::foreignKey(["table" => new Person\Person,"null" => 0]),
                "product_id" => Model::foreignKey(["table" => new Product\Product,"null" => 0]),
                "quantity" => Model::integer(["length" => 20])];
        }

        protected function name() {
            return "purchase";
        }
    }
}
