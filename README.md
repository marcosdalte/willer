willer
===========
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/c8ab021d-3302-4ed7-a17d-8118190b0774/mini.png)](https://insight.sensiolabs.com/projects/c8ab021d-3302-4ed7-a17d-8118190b0774)
[![Build Status](https://travis-ci.org/williamborba/willer.svg?branch=master)](https://travis-ci.org/williamborba/willer)
[![Latest Stable Version](https://poser.pugx.org/wborba/willer/v/stable)](https://packagist.org/packages/wborba/willer) [![Total Downloads](https://poser.pugx.org/wborba/willer/downloads)](https://packagist.org/packages/wborba/willer) [![Latest Unstable Version](https://poser.pugx.org/wborba/willer/v/unstable)](https://packagist.org/packages/wborba/willer) [![License](https://poser.pugx.org/wborba/willer/license)](https://packagist.org/packages/wborba/willer)
[![Coverage Status](https://coveralls.io/repos/github/williamborba/willer/badge.svg?branch=master)](https://coveralls.io/github/williamborba/willer?branch=master)

## Willer Framework

Willer is a PHP framework, highlighting the features of ORM, MVC and Bundle.

## Requisites & Dependencies

* PHP >= 7.1
* Swoole

## Features

* ORM
* MVC
* Run immediately, php server built-in integrated with Swoole, simply `./server.sh`
* Bundle

## Download & Install

* GIT: `git clone` the [GitHub project page](https://github.com/williamborba/willer/)
* Composer: `composer create-project wborba/willer`

## Highlights

### Routes

Routes in single file `Url.php`, Example:

```php
namespace Application\MyApp {
    class Url {
        static public function url() {
            $url = [
                '/'                           => ['Test\index',['GET'],'myapp_test_id_of_url'],
                '/page/test'                  => ['Test\index',['GET'],'id_is_unique'],
                '/page/test/{test_id:[0-9]+}' => ['Test\index',['GET'],'id_of_this_url'],
            ];

            return $url;
        }
    }
}

```
### Models

Models Django like style.

Example sql.
```sql
CREATE TABLE employee (
    id INTEGER NOT NULL,
    employeerole_id INTEGER,
    name TEXT(2000000000) NOT NULL,
    description TEXT(2000000000),
    email TEXT(2000000000) NOT NULL,
    phone TEXT(2000000000) NOT NULL,
    dateCreate TEXT(2000000000) NOT NULL,
    status INTEGER NOT NULL,
    CONSTRAINT employee_pk PRIMARY KEY (id),
    CONSTRAINT FK_employee_employeerole FOREIGN KEY (employeerole_id) REFERENCES employeerole(id)
);

CREATE TABLE employeerole (
    id INTEGER NOT NULL,
    name TEXT(2000000000) NOT NULL,
    description TEXT(255),
    dateCreate TEXT(20) NOT NULL,
    status INTEGER NOT NULL,
    CONSTRAINT employeerole_pk PRIMARY KEY (id)
);
```
Model for table `employee`:

```php
namespace Application\MyApp\Model {
    use Core\{Model,Util};
    use Core\Exception\WException;
    use Application\MyApp\Model\EmployeeRole as ModelEmployeeRole;
    use \Datetime as Datetime;
    use \Exception as Exception;

    class Employee extends Model {
        public const STATUS_ACTIVE = '1';
        public const STATUS_LIST = ['1' => 'Active','0' => 'Inactive'];

        public $id;
        public $employeerole_id;
        public $name;
        public $description;
        public $email;
        public $phone;
        public $dateCreate;
        public $status;

        public function schema() {
            return [
                'id' => Model::primaryKey(['label' => 'ID']),
                'employeerole_id' => Model::foreignKey(['table' => new EmployeeRole,'filter' => ['employeerole.status' => [self::STATUS_ACTIVE]],'label' => 'Office']),
                'name' => Model::char(['length' => 255,'label' => 'Name']),
                'description' => Model::text(['null' => true,'label' => 'Describe']),
                'email' => Model::char(['length' => 100,'label' => 'Email']),
                'phone' => Model::char(['length' => 20,'label' => 'Phone']),
                'dateCreate' => Model::datetime(['hidden' => true,'label' => 'Date registry']),
                'status' => Model::boolean(['label' => 'Active','option' => self::STATUS_LIST]),];
        }

        protected function name() {
            return 'employee';
        }
    }
}
```

### Controller

It's very simple, look at this:

```php
namespace Application\MyApp\Controller {
    use Core\{Controller,Request,Response};
    use Core\Exception\WException;
    use Core\DAO\Transaction;
    use Application\MyApp\Model\Employee as ModelEmployee;
    use Component\HtmlBlock;
    use \Exception as Exception;

    class Test extends Controller {
        public function __construct(Request $request) {
            parent::__construct($request);
        }

        public function index() {
            $response = new Response();
            $transaction = new Transaction();
            $model_employee = new ModelEmployee($transaction);

            $request = $this->getRequest();
            $flash_message = $response->getFlashMessage();

            $html_block = new HtmlBlock\HtmlBlock();

            // ... html block is the interface generator for willer

            $response->setCode(200);

            return $response->render($html_block_render_html);
        }
    }
}
```

### ORM

It's simple and power, try it:

```php
$transaction = new Transaction();

$model_employee = new ModelEmployee($transaction);
$model_employeerole = new ModelEmployeeRole($transaction);

$model_employeerole->get([
    'employeerole.id' => 7]);

$model_employee->save([
    'employeerole_id' => $model_employeerole,
    'name' => 'name of teste',
    'description' => 'description of test',
    'email' => 'email of test',
    'phone' => '123456',
    'dateCreate' => '2017-01-01 00:00:00',
    'status' => true]);

$employee_list = $model_employee
    ->where([
        'employee.status' => ['0','1'],
        'employee.name' => 'name of test',
        'employeerole.name' => 'role of test',
    ])
    ->like([
        'employee.description' => 'my description ...',
    ])
    ->limit(1,10)
    ->orderBy(['employee.id' => 'desc'])
    ->execute([
        'join' => 'left']);

$model_employee->get([
    'employee.id' => 1,]);

$model_employeerole->get([
    'employeerole.id' => 7]);

$model_employee->employeerole_id = $model_employeerole;
$model_employee->name = 'name of teste';
$model_employee->description = 'description of test';
$model_employee->email = 'email of test';
$model_employee->phone = '123456';
$model_employee->status = true;
$model_employee->save();

$model_employee->get([
    'employee.id' => 2,]);

$model_employee->delete();

$model_employee->dumpQuery();
$model_employeerole->dumpQuery();
```

## License

The MIT License (MIT). Please see [License File](https://github.com/williamborba/willer/blob/master/LICENSE) for more information.
