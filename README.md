willer
===========

micro-framework php

Recursos:
- ORM/DAO: 90%
- Multiplos Aplicativos: 100%
- URL clean: 100%
- REST: 90%
- Mysql e Postgres: 90% 

Recursos em fase de desenvolvimento:
- Suporte há SQLite e Mongodb
- Template engine Twig
- ThirdParty de terceiros

Exemplo de Model: (Inspirado pelo framework em python Django)

Namespace Application\Log\Model\Log\ErrorType
```
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
```

Namespace Application\Log\Model\Log\Error
```
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
```

Namespace Application\Log\Model\Log\User
```
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
```

Namespace Application\Log\Model\Log\Register
```
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
```

Feitos os model, a consulta fica facil.

Criando registro
```
$log_errortype->save([
  "name" => "test",
  "describe" => "test describe"]);

$log_error->save([
  "name" => "test",
  "tipo_id" => $log_errortype, //foreign Key ErrorType
  "describe" => "test describe"]);

$log_user->save([
  "name" => "test",
  "describe" => "test describe"]);
```
ou
```
$log_error->name = "test";
$log_error->save();
```

Buscando um registro especifico
```
$log_error->get([
    "name" => "test"]);
```

Criando um registro com foreign Key
```
$log_register->save([
    "user_id" => $log_user, //foreign Key User
    "error_id" => $log_error, //foreign Key Error
    "url" => "test",
    "post" => "{}",
    "get" => "{}",
    "message" => "message of test",
    "dateadd" => Util::datetimeNow()]);
```

Removendo registros
```
$log_error->delete();
$log_user->delete();
$log_register->delete();
```
ou
```
$log_error->delete([
    "id" => $log_error->id]);

$log_user->delete([
    "id" => $log_user->id]);

$log_register->delete([
    "id" => $log_register->id]);

$log_register
  ->where(["register.id" => [11,10,9,8,1]])
  ->delete();
```

Atualizando registros
```
$log_register
  ->where(["register.id" => [11,10,9,8,1]])
  ->update("url" => "blabla-bla blaaaaa");
```

Consulta completa
```
$log_register_list = $log_register
    ->where(["register.id" => [11,10,9,8,1]])
    ->orderBy(["id" => "asc"])
    ->limit($page = 1,$limit = 5)
    ->execute(["join" => "left"]);
```

Operações de CRUD no resultado de consultadas
```
foreach ($log_register_list as $i => $obj_register) {
    $obj_register->url = "1231232ewewrf";
    $obj_register->save();

    print $obj_register->error_id; // retorna o objeto Error referente há Register

    $obj_register->error_id->describe = "alallalalalal"; // atualizando Error
    $obj_register->error_id->save();

    $obj_register->error_id->type_id->name = "uouououou"; // atualizando ErrorType
    $obj_register->error_id->type_id->save();

}
```

Printando ultima query de ErrorType
```
$log_errortype->lastQuery();
```

Mais recursos em breve!
