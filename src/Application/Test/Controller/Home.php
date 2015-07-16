<?php

namespace Application\Test\Controller {
    use \Exception as Exception;
    use \Core\Util;
    use \Core\Controller;
    use \Core\DAO\Transaction;
    use \Application\Test\Model\Test;
    use \Application\Test\Model\Person;

    class Home extends Controller {
        public function __construct() {
            $this->transaction_default = new Transaction(DB_DEFAULT);
        }

        public function index() {
            $person = new Test\Person($this->transaction_default);
            $purchase = new Test\Purchase($this->transaction_default);
            $product = new Test\Product($this->transaction_default);

            try {
                $this->transaction_default->beginTransaction();

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
                        "product.name" => [$product->name]
                        ])
                    ->orderBy([
                        "person.first_name" => "desc"
                        ])
                    ->limit(1,5)
                    ->execute([
                        "join" => "left"]);

                foreach ($purchase_filter as $i => $purchase_obj) {
                    $purchase_obj->product_id->name = "whiskey";
                    $purchase_obj->product_id->save();

                    $purchase_obj->person_id->last_name = "rosa borba";
                    $purchase_obj->person_id->save();

                    $purchase_obj->quantity = 4;
                    $purchase_obj->save();

                }

                $this->transaction_default->commit();

            } catch (Exception $error) {
                $this->transaction_default->rollBack();

                throw new Exception($error);
            }

            Util::renderTojson($purchase_filter);
        }

        public function test() {
            print "okoookokokokokok";
        }

        public function tpl($var,$var2) {
            // $loader = new Twig_Loader_Filesystem("/path/to/templates");
            // $twig = new Twig_Environment($loader,array(
            //     "cache" => "/path/to/compilation_cache",
            // ));
            //
            // echo $twig->render("index.html",array("name" => "Fabien"));

            $person = new Person\Person($this->transaction_default);

            $this->transaction_default->connect();

            $person_filter = $person
                ->where()
                ->orderBy()
                ->limit(1,5)
                ->execute();

            print $var."<br/><br/>";
            print $var2."<br/><br/>";

            $loader = new \Twig_Loader_Array(array(
                'index' => 'Hello {{ name }}!',
            ));

            $twig = new \Twig_Environment($loader);

            echo $twig->render('index', array('name' => 'Fabien'));
        }
    }
}
