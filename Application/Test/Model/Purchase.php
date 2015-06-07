<?php

namespace Application\Test\Model\Purchase {
    use \Core\Model;
    use \Application\Test\Model\Client;

    class Purchase extends Model {
        public $id;
        public $client_id;
        public $product;

        protected function schema() {
            return [
                "id" => Model::primaryKey(),
                "client_id" => Model::foreignKey(["table" => new Client\Client,"null" => 0]),
                "product" => Model::char(["length" => 40]),
        }

        protected function name() {
            return "purchase";
        }
    }
}
