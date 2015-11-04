<?php

namespace Application\Restaurant\Controller {
    use \Core\Controller;
    use \Core\DAO\Transaction;
    use \Core\Util;
    use \Application\Restaurant\Model\Restaurant;

    class Home extends Controller {
        private $db_transaction;

        public function __construct($request_method = null) {
            parent::__construct($request_method);

            $this->db_transaction = new Transaction();
        }

        public function requestMethodTest() {
            print 'ok';
        }

        public function index() {
            $restaurant = new Restaurant($this->db_transaction);

            $this->db_transaction->connect();

            // $restaurant->delete();

            // $restaurant->get([
            //     'id' => '25']);

            // $restaurant->delete(['id' => 123]);

            // $restaurant->place = 'bla e bla';
            // $restaurant->save();

            // $restaurant->update([
            //     'place' => 132113]);

            $restaurant->save([
                'place' => 'ad',
                'serves_hot_dogs' => 1,
                'serves_pizza' => 1,]);

            // $restaurant_list = $restaurant
            //     ->where([
            //         'restaurant.id' => [15,16],])
            //     ->orderBy([
            //         'restaurant.serves_pizza' => 'desc'])
            //     ->limit(1,5)
            //     ->update([
            //         'place' => 'testandiooo123654'])
            //     ->execute([
            //         'join' => 'left']);

            // Util::renderToJson($restaurant->dumpQuery());
            Util::renderToJson($restaurant);
            // Util::renderToJson($restaurant_list);
        }
    }
}
