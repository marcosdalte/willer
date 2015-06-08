<?php

namespace Application\Test\Controller {
    use \Exception as Exception;
    use \Core\Util;
    use \Core\TplEngine;
    use \Core\Controller;
    use \Core\DAO\Transaction;
    use \Application\Test\Model\Person;
    use \Application\Test\Model\Purchase;

    class Home extends Controller {
        public function __construct($request_method = null) {
            parent::__construct($request_method);

            $this->transaction_default = new Transaction(DB_DEFAULT);
        }

        public function index($url_fragment) {
            $person = new Person\Person($this->transaction_default);
            $purchase = new Purchase\Purchase($this->transaction_default);

            try {
                $this->transaction_default->beginTransaction();

                $person->name = "william";
                $person->save();

                $purchase->save([
                    "person_id" => $person,
                    "product" => "beer"]);

                $purchase_filter = $purchase
                    ->where([
                        "person_id" => $person->id
                        ])
                    ->orderBy([
                        "person.name" => "desc"
                        ])
                    ->limit(1,5)
                    ->execute([
                        "join" => "left"]);

                foreach ($purchase_filter as $i => $purchase_obj) {
                    $purchase_obj->product = "whiskey";
                    $purchase_obj->save();

                    $purchase_obj->person_id->name = "william borba";
                    $purchase_obj->person_id->save();

                }

                $this->transaction_default->commit();

            } catch (Exception $error) {
                $this->transaction_default->rollBack();

                throw new Exception($error);
            }

            Util::renderTojson($purchase_filter);
        }
    }
}
