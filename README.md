willer
===========

framework php

# Quick start

In this section are some definitions to be respected for the correct functioning of the willer, we present the three main layers - `Model, Controllers and View` - in addition to working with routes in` url.php`

## URL route

Na raiz do willer temos o arquivo `url.php`, neste serão definidos os padrões de URL, ligando cada padrão há uma `aplicação/controller/view`

Example:

```php
// url's frontend
$URL = [
    "/^\/?$/"                     => ["MyFrontend/Home/index",null],
    "/^home\/?$/"                 => ["MyFrontend/Company/index",null],
    "/^product\/?$/"              => ["MyFrontend/Product/index",null],
    "/^product\/([a-z0-9]+)\/?$/" => ["MyFrontend/Product/detail",null],
    "/^contato\/?$/"              => ["MyFrontend/Contact/contact",null],
];

// ajax requests
// limiting the request by the HTTP protocol type (REST)
$URL += [
    "/^request\/product\/?$/"           => ["MyFrontend/Request/productList",["GET"]],
    "/^request\/product\/([0-9]+)\/?$/" => ["MyFrontend/Request/productDetail",["GET"]],
    "/^request\/product\/add\/?$/"      => ["MyFrontend/Request/productAdd",["POST"]],
];

// url's backend
$URL += [
    "/^admin\/?$/" => ["MyBackend/Dashboard/index",null],
];

// blog
$URL += [
    "/^blog\/?$/"            => ["MyBlog/Blog/index",null],
    "/^blog\/([\w\d]+)\/?$/" => ["MyBlog/Blog/detail",null],
];

```

## The model layer

Exemplo das tabela Pessoa e Produto que se relacionam com a tabela Compras
```sql
CREATE TABLE `person` (
	`id`	INTEGER PRIMARY KEY AUTOINCREMENT,
	`first_name`	TEXT,
	`last_name`	TEXT
);

CREATE TABLE `product` (
	`id`	INTEGER PRIMARY KEY AUTOINCREMENT,
	`name`	TEXT NOT NULL,
	`price`	REAL NOT NULL
);

CREATE TABLE `purchase` (
	`id`	INTEGER PRIMARY KEY AUTOINCREMENT,
	`person_id`	INTEGER NOT NULL,
	`product_id`	NUMERIC NOT NULL,
	`quantity`	INTEGER NOT NULL
);
```
Agora o exemplo da estrutura dos model, conforme a entidade relacional das tabelas

```php
namespace Application\Test\Model\Person {
    use \Core\Model;

    class Person extends Model {
        public $id;
        public $first_name;
        public $last_name;

        protected function schema() {
            return [
                "id" => Model::primaryKey(),
                "first_name" => Model::char(["length" => 40]),
                "last_name" => Model::char(["length" => 40])];
        }

        protected function name() {
            return "person";
        }
    }
}

namespace Application\Test\Model\Product {
    use \Core\Model;

    class Product extends Model {
        public $id;
        public $name;
        public $price;

        protected function schema() {
            return [
                "id" => Model::primaryKey(),
                "name" => Model::char(["length" => 40]),
                "price" => Model::float(["length" => 20])];
        }

        protected function name() {
            return "product";
        }
    }
}

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
```

In controller temos algumas simples querys

```php
$db_transaction = new Transaction(DB_POSTGRES);

$person = new Person\Person($db_transaction);
$product = new Product\Product($db_transaction);
$purchase = new Purchase\Purchase($db_transaction);

try {
    $db_transaction->beginTransaction();

    $product->save([
        "name" => "beer",
        "price" => 1.99,
        ]);

    $person->save([
        "first_name" => "wilian",
        "last_name" => "borba",
        ]);

    // update
    $person->first_name = "william";
    $person->save();

    $purchase->save([
        "person_id" => $person,
        "product_id" => $product,
        "quantity" => 3]);

    $purchase_filter = $purchase
        ->where([
            "person.id" => $person->id,
            "product.name" => [$product->name] // values arrays result in 'IN' sql operator
            ])
        ->orderBy([
            "person.first_name" => "desc"
            ])
        ->limit(1,5)
        ->execute([
            "join" => "left"]);

    $db_transaction->commit();

} catch (Exception $error) {
    $db_transaction->rollBack();

    throw new Exception($error);
}

/*return of purchase_filter

[
    {
        "id": "18",
        "person_id": {
            "id": "18",
            "first_name": "william",
            "last_name": "rosa borba"
        },
        "product_id": {
            "id": "18",
            "name": "whiskey",
            "price": "1.99"
        },
        "quantity": "4"
    }
]

*/
```
Cada index da lista é um registro mapeado, representado por um objeto da mesma entidade, contendo todas as funções CRUD.

```php
foreach ($purchase_filter as $i => $purchase_obj) {
    $purchase_obj->product_id->name = "whiskey";
    $purchase_obj->product_id->save();

    print_r($purchase_obj->person_id); // retorna o objeto Person referenciado por Purchase

    $purchase_obj->person_id->last_name = "rosa borba";
    $purchase_obj->person_id->save();

    $purchase_obj->quantity = 4;
    $purchase_obj->save();

}
```

Para cada query efetuada podemos a qualquer momento fazer um debug da consulta realizada.

```php
$purchase->lastQuery();
```
