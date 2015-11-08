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

        public function crud() {
            // load model with Transaction instance
            $restaurant = new Restaurant($this->db_transaction);

            // open connection
            $this->db_transaction->connect();

            // save
            $restaurant->save([
                'place' => 'place of test',
                'serves_hot_dogs' => 1,
                'serves_pizza' => 1,]);

            // update
            // $restaurant->place = 'bla e bla';
            // $restaurant->save();

            // delete
            // $restaurant->delete();

            // get(unique)
            // $restaurant->get([
            //     'id' => '25']);

            // delete with filter
            // $restaurant->delete(['id' => 123]);

            // update
            // $restaurant->update([
            //     'place' => 'place update']);

            // select with where, order by, limit(pagination) and join left
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

            // select with update and return changes
            $restaurant_list = $restaurant
                ->where([
                    'restaurant.id' => [1,2],]) // id in(1,2)
                ->orderBy([
                    'restaurant.serves_pizza' => 'desc'])
                ->limit(1,5) // page 1 limit 5
                ->update([
                    'place' => 'place update yea!']) // update in current select
                ->execute([
                    'join' => 'left']); // join left|right optional

            // list of query's
            // Util::renderToJson($restaurant->dumpQuery());

            // render to json result
            // Util::renderToJson($restaurant);
            Util::renderToJson($restaurant_list);
        }

        public function otherView() {
            print 'test other view';
        }
    }
}
