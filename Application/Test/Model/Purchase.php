<?php

namespace Application\Test\Model\Purchase {
    use \Core\Model;
    use \Application\Test\Model\Person;

    class Purchase extends Model {
        public $id;
        public $person_id;
        public $product;

        protected function schema() {
            return [
                "id" => Model::primaryKey(),
                "person_id" => Model::foreignKey(["table" => new Person\Person,"null" => 0]),
                "product" => Model::char(["length" => 40])];
        }

        protected function name() {
            return "purchase";
        }
    }
}
