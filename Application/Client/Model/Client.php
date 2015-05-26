<?php

namespace Application\Client\Model\Client {
    use \Core\Model;

    class Client extends Model {
        public $id;
        public $nome;
        public $descricao;

        protected function schema() {
            return [
                "id" => Model::primaryKey(),
                "nome" => Model::char(),
                "descricao" => Model::text()];
        }

        protected function name() {
            return "cliente";
        }
    }
}
