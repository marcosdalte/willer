<?php

namespace Application\Restaurant\Controller {
    use \Core\Controller;
    use \Core\DAO\Transaction;
    use \Application\Restaurant\Model\Restaurant;

    class Home extends Controller {
        private $db_transaction;

        public function __construct($request_method = null) {
            parent::__construct($request_method);

            $this->db_transaction = new Transaction();
        }

        public function index() {
            $restaurant = new Restaurant($this->db_transaction);

            $this->db_transaction->connect();

            $restaurant->save([
                'place' => 'lalala',
                'serves_hot_dogs' => 1,
                'serves_pizza' => 1,]);

            print 'ok';
        }
    }
}