<?php

namespace Application\Container\Model\Container {
    use \Core\Model;

    class Container extends Model {
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
            return "container";
        }
    }
}
