willer
===========
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/c8ab021d-3302-4ed7-a17d-8118190b0774/mini.png)](https://insight.sensiolabs.com/projects/c8ab021d-3302-4ed7-a17d-8118190b0774)
[![Build Status](https://travis-ci.org/williamborba/willer.svg?branch=master)](https://travis-ci.org/williamborba/willer)
[![Latest Stable Version](https://poser.pugx.org/wborba/willer/v/stable)](https://packagist.org/packages/wborba/willer) [![Total Downloads](https://poser.pugx.org/wborba/willer/downloads)](https://packagist.org/packages/wborba/willer) [![Latest Unstable Version](https://poser.pugx.org/wborba/willer/v/unstable)](https://packagist.org/packages/wborba/willer) [![License](https://poser.pugx.org/wborba/willer/license)](https://packagist.org/packages/wborba/willer)

### php framework - influenced by django and codeigniter 

### Documentation

http://williamborba.github.io/willer

### Highlights

Routes in style Django by single file `url.php`. Example.

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
### Model's Django like style.

Example sql.
```sql
CREATE TABLE `place` (
    `id`    INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
    `name`  TEXT NOT NULL,
    `address`   TEXT NOT NULL
);

CREATE TABLE `restaurant` (
    `id`    INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
    `place_id`  TEXT,
    `name`  TEXT NOT NULL,
    `serves_hot_dogs`   INTEGER NOT NULL,
    `serves_pizza`  INTEGER NOT NULL,
    FOREIGN KEY(`place_id`) REFERENCES place
);

CREATE TABLE `waiter` (
    `id`    INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
    `restaurant_id` INTEGER,
    `name`  TEXT NOT NULL,
    FOREIGN KEY(`restaurant_id`) REFERENCES restaurant
);
```
Example model class Place, Restaurant and Waiter.

| File  | Namespace/Class |
| :------------ |:---------------:|
| `Application/Restaurant/Model/Place.php` | `Application\Restaurant\Model\Place` |

```php
<?php

namespace Application\Restaurant\Model {
    use \Core\Model;

    class Place extends Model {
        public $id;
        public $name;
        public $address;

        protected function schema() {
            return [
                'id' => Model::primaryKey(),
                'name' => Model::char(['length' => 40]),
                'address' => Model::char(['length' => 40]),];
        }

        protected function name() {
            return 'place';
        }
    }
}
```

| File  | Namespace/Class |
| :------------ |:---------------:|
| `Application/Restaurant/Model/Restaurant.php` | `Application\Restaurant\Model\Restaurant` |

```php
<?php

namespace Application\Restaurant\Model {
    use \Core\Model;
    use \Application\Restaurant\Model\Place;

    class Restaurant extends Model {
        public $id;
        public $place_id;
        public $name;
        public $serves_hot_dogs;
        public $serves_pizza;

        protected function schema() {
            return [
                'id' => Model::primaryKey(),
                'place_id' => Model::foreignKey(['table' => new Place,'null' => true]),
                'name' => Model::char(['length' => 40]),
                'serves_hot_dogs' => Model::boolean(['null' => false]),
                'serves_pizza' => Model::boolean(['null' => false]),];
        }

        protected function name() {
            return 'restaurant';
        }
    }
}
```

| File  | Namespace/Class |
| :------------ |:---------------:|
| `Application/Restaurant/Model/Waiter.php` | `Application\Restaurant\Model\Waiter` |

```php
<?php

namespace Application\Restaurant\Model {
    use \Core\Model;
    use \Application\Restaurant\Model\Restaurant;

    class Waiter extends Model {
        public $id;
        public $restaurant_id;
        public $name;

        protected function schema() {
            return [
                'id' => Model::primaryKey(),
                'restaurant_id' => Model::foreignKey(['table' => new Restaurant,'null' => true]),
                'name' => Model::char(['length' => 40]),];
        }

        protected function name() {
            return 'waiter';
        }
    }
}
```

### ORM engine, style Django and Active Records.

Controller `Home.php`

| File  | Namespace/Class |
| :------------ |:---------------:|
| `Application/Restaurant/Controller/Home.php` | `Application\Restaurant\Controller\Home` |

```php
<?php

namespace Application\Restaurant\Controller {
    use \Core\Controller;
    use \Core\DAO\Transaction;
    use \Core\Util;
    use \Application\Restaurant\Model\Place;
    use \Application\Restaurant\Model\Restaurant;
    use \Application\Restaurant\Model\Waiter;

    class Home extends Controller {
        private $db_transaction;

        public function __construct($request_method = null) {
            parent::__construct($request_method);

            // load transaction object
            $this->transaction = new Transaction();
        }

        public function restaurantAdd() {
            // load model with Transaction instance
            $restaurant = new Restaurant($this->transaction);
            $place = new Place($this->transaction);
            $waiter = new Waiter($this->transaction);

            // open connection
            $this->transaction->connect();

            // save place
            $place->save([
                'name' => 'place name test',
                'address' => 'place address test',]);

            // save restaurant
            $restaurant->save([
                'place_id' => $place,
                'name' => 'restaurant name test',
                'serves_hot_dogs' => 1,
                'serves_pizza' => 1,]);

            // save waiter
            $waiter->save([
                'restaurant_id' => $restaurant,
                'name' => 'waiter name test']);
        }

        public function restaurantUpdate() {
            // load model with Transaction instance
            $restaurant = new Restaurant($this->transaction);
            $place = new Place($this->transaction);
            $waiter = new Waiter($this->transaction);

            // open connection
            $this->transaction->connect();

            // save place
            $place->save([
                'name' => 'place name test',
                'address' => 'place address test',]);

            // update place
            $place->name = 'place name update test';
            $place->address = 'place address update test';
            $place->save();

            // save restaurant
            $restaurant->save([
                'place_id' => $place,
                'name' => 'restaurant name test',
                'serves_hot_dogs' => 1,
                'serves_pizza' => 1,]);

            // update restaurant
            $restaurant->name = 'restaurant name update test';
            $restaurant->serves_hot_dogs = 0;
            $restaurant->serves_pizza = 0;
            $restaurant->save();

            // save waiter
            $waiter->save([
                'restaurant_id' => $restaurant,
                'name' => 'waiter name test']);

            // update waiter
            $waiter->name = 'waiter name update test';
            $waiter->save();
        }

        public function restaurantDelete() {
            // load model with Transaction instance
            $restaurant = new Restaurant($this->transaction);
            $place = new Place($this->transaction);
            $waiter = new Waiter($this->transaction);

            // open connection
            $this->transaction->connect();

            // save place
            $place->save([
                'name' => 'place name test',
                'address' => 'place address test',]);

            // save restaurant
            $restaurant->save([
                'place_id' => $place,
                'name' => 'restaurant name test',
                'serves_hot_dogs' => 1,
                'serves_pizza' => 1,]);

            // save waiter
            $waiter->save([
                'restaurant_id' => $restaurant,
                'name' => 'waiter name test']);

            // delete place register
            $place->delete();

            // delete place restaurant
            $restaurant->delete();

            // delete place waiter
            $waiter->delete();
        }

        public function restaurantGet() {
            // load model with Transaction instance
            $restaurant = new Restaurant($this->transaction);
            $place = new Place($this->transaction);
            $waiter = new Waiter($this->transaction);

            // open connection
            $this->transaction->connect();

            // delete if exists place
            $place->delete([
                'name' => 'place_name_unique',
                'address' => 'place_address_unique']);

            // delete if exists restaurant
            $restaurant->delete([
                'name' => 'restaurant_name_unique']);

            // delete if exists waiter
            $waiter->delete([
                'name' => 'waiter_name_unique']);

            // save place
            $place->save([
                'name' => 'place_name_unique',
                'address' => 'place_address_unique',]);

            // save restaurant
            $restaurant->save([
                'place_id' => $place,
                'name' => 'restaurant_name_unique',
                'serves_hot_dogs' => 1,
                'serves_pizza' => 1,]);

            // save waiter
            $waiter->save([
                'restaurant_id' => $restaurant,
                'name' => 'waiter_name_unique']);

            // get place unique register
            $place->get([
                'place.name' => 'place_name_unique',
                'place.address' => 'place_address_unique']);

            // get restaurant unique register
            $restaurant->get([
                'restaurant.name' => 'restaurant_name_unique']);

            // get waiter unique register
            $waiter->get([
                'waiter.name' => 'waiter_name_unique']);
        }

        public function restaurantSelect() {
            // load model with Transaction instance
            $restaurant = new Restaurant($this->transaction);
            $place = new Place($this->transaction);
            $waiter = new Waiter($this->transaction);

            // open connection
            $this->transaction->connect();

            // delete if exists
            $restaurant->delete();
            $place->delete();
            $waiter->delete();

            // save place
            $place->save([
                'name' => 'place name test',
                'address' => 'place address test',]);

            // save restaurant
            $restaurant->save([
                'place_id' => $place,
                'name' => 'restaurant name test',
                'serves_hot_dogs' => 1,
                'serves_pizza' => 1,]);

            // save waiter
            $waiter->save([
                'restaurant_id' => $restaurant,
                'name' => 'waiter name test']);

            // select with where, order by and limit(pagination)
            $restaurant_list = $restaurant
                ->where([
                    'restaurant.name' => 'restaurant name test',
                    'restaurant.serves_hot_dogs' => [0,1],
                    'restaurant.serves_pizza' => [0,1],
                    'place.name' => 'place name test',
                    'place.address' => 'place address test',])
                ->orderBy([
                    'restaurant.name' => 'desc',
                    'place.name' => 'desc',
                    'place.address' => 'desc',])
                ->limit(1,5)
                ->execute();

            // select with where, order by and limit(pagination)
            $place_list = $place
                ->where([
                    'place.name' => 'place name test',
                    'place.address' => 'place address test',])
                ->orderBy([
                    'place.name' => 'desc',
                    'place.address' => 'desc',])
                ->limit(1,5)
                ->execute();

            // select with where, order by and limit(pagination)
            $waiter_list = $waiter
                ->where([
                    'waiter.name' => 'waiter name test',
                    'restaurant.name' => 'restaurant name test',
                    'restaurant.serves_hot_dogs' => [0,1],
                    'restaurant.serves_pizza' => [0,1],])
                ->orderBy([
                    'restaurant.name' => 'desc',
                    'waiter.name' => 'desc',])
                ->limit(1,5)
                ->execute();
        }
    }
}
```
Retrieve query's history in real time.

```php
// return restaurant query's
$restaurant->dumpQuery();

// return place query's
$place->dumpQuery();

// return waiter query's
$waiter->dumpQuery();
```
