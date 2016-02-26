willer
===========
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/c8ab021d-3302-4ed7-a17d-8118190b0774/mini.png)](https://insight.sensiolabs.com/projects/c8ab021d-3302-4ed7-a17d-8118190b0774)
[![Build Status](https://travis-ci.org/williamborba/willer.svg?branch=master)](https://travis-ci.org/williamborba/willer)
[![Latest Stable Version](https://poser.pugx.org/wborba/willer/v/stable)](https://packagist.org/packages/wborba/willer) [![Total Downloads](https://poser.pugx.org/wborba/willer/downloads)](https://packagist.org/packages/wborba/willer) [![Latest Unstable Version](https://poser.pugx.org/wborba/willer/v/unstable)](https://packagist.org/packages/wborba/willer) [![License](https://poser.pugx.org/wborba/willer/license)](https://packagist.org/packages/wborba/willer)

## Willer php framework

Willer is a PHP framework, it was created based on ideas coming from other frameworks like Django(python), Codeigniter(php) and ZendFramework(php).

## Requisites & Dependencies

* PHP >= 5.6(compatible with php7)
* Whoops - php errors for cool kids
* PHPUnit - The PHP Testing Framework(require-dev)
* Release - PHP library to increment package version and release project(require-dev)

## Features

* ORM
* MVC
* Run immediately, php server built-in integrated, like `./runserver.sh`
* Bundle

## Download & Install

* GIT: `git clone` the [GitHub project page](https://github.com/williamborba/willer/)
* Composer: `composer create-project wborba/willer`

## Highlights

### Routes

Routes in single file `Application/Restaurant/Url.php`. Example.

```php
<?php

namespace Application\Restaurant {
    class Url {
        static public function url() {
            return [
                "/^\/?$/"                     => ["Restaurant/Home/index",null],
                "/^home\/?$/"                 => ["Restaurant/Company/index",null],
                "/^product\/?$/"              => ["Restaurant/Product/index",null],
                "/^product\/([a-z0-9]+)\/?$/" => ["Restaurant/Product/detail",null],
                "/^contato\/?$/"              => ["Restaurant/Contact/contact",null],
            ];
        }
    }
}
```
### Models

Models Django like style.

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
| :------------: |:---------------:|
| `Application/Restaurant/Model/Place.php` | `Application\Restaurant\Model\Place` |

```php
<?php

namespace Application\Restaurant\Model {
    use Core\Model;

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
| :------------: |:---------------:|
| `Application/Restaurant/Model/Restaurant.php` | `Application\Restaurant\Model\Restaurant` |

```php
<?php

namespace Application\Restaurant\Model {
    use Core\Model;
    use Application\Restaurant\Model\Place;

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
| :------------: |:---------------:|
| `Application/Restaurant/Model/Waiter.php` | `Application\Restaurant\Model\Waiter` |

```php
<?php

namespace Application\Restaurant\Model {
    use Core\Model;
    use Application\Restaurant\Model\Restaurant;

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

### Controller

ORM engine.

Controller `Home.php` with method/view `restaurantAdd` contains transaction example.

| File  | Namespace/Class |
| :------------: |:---------------:|
| `Application/Restaurant/Controller/Home.php` | `Application\Restaurant\Controller\Home` |

```php
<?php

namespace Application\Restaurant\Controller {
    use Core\Controller;
    use Core\DAO\Transaction;
    use Core\Util;
    use Application\Restaurant\Model\Place;
    use Application\Restaurant\Model\Restaurant;
    use Application\Restaurant\Model\Waiter;

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

            try {
                // open connection with begin transaction
                $this->transaction->beginTransaction();

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

                // commit
                $this->transaction->commit();

            } catch (Exception $error) {
                // rollBack
                $this->transaction->rollBack();
            }
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

## License

The MIT License (MIT). Please see [License File](https://github.com/williamborba/willer/blob/master/LICENSE) for more information.
