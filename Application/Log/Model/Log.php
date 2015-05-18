<?php

namespace Application\Log\Model\Log {
    use \Core\Model;

    class ErrorType extends Model {
        public $id;
        public $nome;

        protected function schema() {
            return [
                "id" => Model::primaryKey(),
                "nome" => Model::char(["length" => 40])];
        }

        protected function name() {
            return "errotipo";
        }
    }

    class Error extends Model {
        public $id;
        public $tipo_id;
        public $nome;
        public $descricao;

        protected function schema() {
            return [
                "id" => Model::primaryKey(),
                "tipo_id" => Model::foreignKey(["table" => new ErrorType,"null" => 0]),
                "nome" => Model::char(["length" => 40]),
                "descricao" => Model::text(["null" => 1])];
        }

        protected function name() {
            return "erro";
        }
    }

    class User extends Model {
        public $id;
        public $ativo;
        public $nome;
        public $chavepublica;
        public $data;
        public $datacancelamento;

        protected function schema() {
            return [
                "id" => Model::primaryKey([]),
                "ativo" => Model::integer(["length" => 1]),
                "nome" => Model::char(["length" => 30]),
                "chavepublica" => Model::char(["length" => 40]),
                "data" => Model::datetime([]),
                "datacancelamento" => Model::datetime(["null" => 1])];
        }

        protected function name() {
            return "usuario";
        }
    }

    class Register extends Model {
        public $id;
        public $usuario_id;
        public $erro_id;
        public $url;
        public $post;
        public $get;
        public $data;

        protected function schema() {
            return [
                "id" => Model::primaryKey([]),
                "usuario_id" => Model::foreignKey(["table" => new User,"null" => 1]),
                "erro_id" => Model::foreignKey(["table" => new Error,"null" => 1]),
                "url" => Model::char(["length" => 255]),
                "post" => Model::text(["null" => 1]),
                "get" => Model::text(["null" => 1]),
                "data" => Model::datetime([]),];
        }

        protected function name() {
            return "registro";
        }
    }
}
