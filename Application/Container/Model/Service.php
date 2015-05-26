<?php

namespace Application\Container\Model\Service {
    use \Core\Model;

    class Service extends Model {
        public $id;
        public $nome;
        public $descricao;

        protected function schema() {
            return [
                "id" => Model::primaryKey(),
                "nome" => Model::char(["length" => 40]),
                "descricao" => Model::text()];
        }

        protected function name() {
            return "servico";
        }
    }
}
