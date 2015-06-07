<?php

namespace Application\Test\Controller {
    use \Exception as Exception;
    use \Core\Util;
    use \Core\TplEngine;
    use \Core\Controller;
    use \Core\DAO\Transaction;
    use \Application\Test\Model\Client;
    use \Application\Test\Model\Purchase;

    class Home extends Controller {
        public function __construct($request_method = null) {
            parent::__construct($request_method);

            $this->transaction_default = new Transaction(DB_DEFAULT);
        }

        public function index($url_fragment) {
            $client = new Client\Client($this->transaction_default);
            $purchase = new Purchase\Purchase($this->transaction_default);

            try {
                $this->transaction_default->beginTransaction();

                $client->name = "william";
                $client->save();

                $purchase->save([
                    "client_id" => $client,
                    "product" => "radio"]);

                $purchase
                    ->where()
                    ->orderBy()
                    ->limit()
                    ->execute([
                        "join" => "left"]);

                $this->transaction_default->commit();

            } catch (Exception $error) {
                $this->transaction_default->rollBack();

                throw new Exception($error);
            }
        }
    }
}
