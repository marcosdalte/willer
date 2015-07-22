<?php

namespace Application\Test\Controller {
    use \Exception as Exception;
    use \Core\Util;
    use \Core\Controller;
    use \Core\DAO\Transaction;
    use \Application\Test\Model\Test;
    use \Application\Test\Model\Person;
    use \Application\Test\load;

    class Home extends Controller {
        public function __construct($request_method = null) {
            parent::__construct($request_method);

            $this->transaction_default = new Transaction();
        }

        public function index() {
            $person = new Test\Person($this->transaction_default);
            $purchase = new Test\Purchase($this->transaction_default);
            $product = new Test\Product($this->transaction_default);

            try {
                $this->transaction_default->beginTransaction();

                $product->save([
                    'name' => 'beer',
                    'price' => 1.99,
                    ]);

                $person->save([
                    'first_name' => 'wilian',
                    'last_name' => 'borba',
                    ]);

                // update
                $person->first_name = 'william';
                $person->save();

                $purchase->save([
                    'person_id' => $person,
                    'product_id' => $product,
                    'quantity' => 3]);

                $purchase_filter = $purchase
                    ->where([
                        'person.id' => $person->id,
                        'product.name' => [$product->name]
                        ])
                    ->orderBy([
                        'person.first_name' => 'desc'
                        ])
                    ->limit(1,5)
                    ->execute([
                        'join' => 'left']);

                foreach ($purchase_filter as $i => $purchase_obj) {
                    $purchase_obj->product_id->name = 'whiskey';
                    $purchase_obj->product_id->save();

                    $purchase_obj->person_id->last_name = 'rosa borba';
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
            print 'ok, test success!';
        }

        public function tpl($var,$var2) {
            $load = load::load();

            $loader = new \Twig_Loader_Filesystem(ROOT_PATH.'/Application/Test/view');

            $twig = new \Twig_Environment($loader,[
                "cache" => ROOT_PATH.'/Application/Test/view',
            ]);
            
            print $twig->render("home.html",[
                "load" => $load,
                "var1" => $var,
                "var2" => $var2]);
        }
    }
}