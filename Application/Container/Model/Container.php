<?php

namespace Application\Container\Model\Container {
    use \Core\Model;
    use \Application\Container\Model\Service;

    class Container extends Model {
        public $id;
        public $servico_id;
        public $nome;
        public $descricao;

        protected function schema() {
            return [
                "id" => Model::primaryKey(),
                "servico_id" => Model::foreignKey(["table" => new Service\Service]),
                "nome" => Model::char(["length" => 40]),
                "descricao" => Model::text()];
        }

        protected function name() {
            return "container";
        }
    }
}
