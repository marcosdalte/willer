<?php

namespace Application\Restaurant\Controller {
    use Core\Controller;
    use Core\DAO\Transaction;
    use Core\Util;
    use Application\Restaurant\Model\Restaurant;
    use Application\Restaurant\Model\Waiter;
    use Application\Restaurant\Model\Place;
    use Core\Component\HtmlBlock;

    class Home extends Controller {
        private $db_transaction;

        public function __construct($request_method = null) {
            parent::__construct($request_method);

            // load transaction object
            $this->db_transaction = new Transaction();
        }

        public function requestMethodGetTest() {
            print 'ok';
        }

        public function home() {
            $html_block = new HtmlBlock\HtmlBlock();

            $html_table = new HtmlBlock\Table(
                $html_block,[
                    'id' => 'table_id',
                    'class' => 'table']);

            $html = $html_block
                ->setHeadTitleContent('title of test')
                ->addCss('https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css')
                ->addCss('https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap-theme.min.css')
                ->addJs('https://code.jquery.com/jquery-2.2.1.min.js')
                ->addJs('https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js')
                ->appendBodyElement($html_table->getDomElement())
                ->renderHtml();

            print $html;

        }

        public function restaurantAdd() {
            // load model with Transaction instance
            $restaurant = new Restaurant($this->db_transaction);

            // open connection
            $this->db_transaction->connect();

            // save
            $restaurant->save([
                'name' => 'place of test',
                'serves_hot_dogs' => 1,
                'serves_pizza' => 1,]);

            Util::renderToJson($restaurant);
        }

        public function restaurantUpdate() {
            // load model with Transaction instance
            $restaurant = new Restaurant($this->db_transaction);

            // open connection
            $this->db_transaction->connect();

            // save
            $restaurant->save([
                'name' => 'place of test',
                'serves_hot_dogs' => 1,
                'serves_pizza' => 1,]);

            // update
            $restaurant->name = 'bla e bla';
            $restaurant->serves_hot_dogs = 0;
            $restaurant->save();

            Util::renderToJson($restaurant);
        }

        public function restaurantDelete() {
            // load model with Transaction instance
            $restaurant = new Restaurant($this->db_transaction);

            // open connection
            $this->db_transaction->connect();

            // delete all register without filter
            // $restaurant->delete();

            // save
            $restaurant->save([
                'name' => 'place of test',
                'serves_hot_dogs' => 1,
                'serves_pizza' => 1,]);

            // delete current instance
            $restaurant->delete();

            Util::renderToJson($restaurant);
        }

        public function restaurantGet() {
            // load model with Transaction instance
            $restaurant = new Restaurant($this->db_transaction);

            // open connection
            $this->db_transaction->connect();

            // delete all register without filter
            $restaurant->delete();

            // save
            $restaurant->save([
                'name' => 'place of test',
                'serves_hot_dogs' => 1,
                'serves_pizza' => 1,]);

            // get(unique)
            $restaurant->get([
                'restaurant.name' => 'place of test']);

            // delete current instance
            // $restaurant->delete();

            // update
            // $restaurant->name = 'bla e bla';
            // $restaurant->serves_hot_dogs = 0;
            // $restaurant->save();

            Util::renderToJson($restaurant);
        }

        public function restaurantListing() {
            // load model with Transaction instance
            $restaurant = new Restaurant($this->db_transaction);
            $place = new Place($this->db_transaction);
            $waiter = new Waiter($this->db_transaction);

            // open connection
            $this->db_transaction->connect();

            // save place
            $place->save([
                'name' => 'place name test',
                'address' => 'place address test',]);

            // save restaurant
            $restaurant->save([
                'place_id' => $place,
                'name' => 'place of test',
                'serves_hot_dogs' => 1,
                'serves_pizza' => 1,]);

            // select with where, order by, limit(pagination) and join left
            // $restaurant_list = $restaurant
            //     ->where([
            //         'restaurant.id' => [15,16],])
            //     ->orderBy([
            //         'restaurant.serves_pizza' => 'desc'])
            //     ->limit(1,5)
            //     ->execute([
            //         'join' => 'left']);

            // select with update and return changes
            $restaurant_list = $restaurant
                ->where([
                    'restaurant.serves_hot_dogs' => [1,0],]) // id in(1,2)
                ->orderBy([
                    'restaurant.serves_pizza' => 'desc'])
                ->limit(1,5) // page 1 limit 5
                ->update([
                    'name' => 'place update yea!']) // update in current select
                ->execute([
                    'join' => 'left']); // join left|right optional

            // list of query's
            // Util::renderToJson($restaurant->dumpQuery());

            // render to json result
            Util::renderToJson($restaurant_list);
        }

        public function restaurantLikeListing() {
            // load model with Transaction instance
            $restaurant = new Restaurant($this->db_transaction);
            $place = new Place($this->db_transaction);
            $waiter = new Waiter($this->db_transaction);

            // open connection
            $this->db_transaction->connect();

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
                ->like([
                    'restaurant.name' => '%name%',
                    'place.name' => '%test',
                    'place.address' => 'place%',])
                ->orderBy([
                    'restaurant.name' => 'desc',
                    'place.name' => 'desc',
                    'place.address' => 'desc',])
                ->limit(1,5)
                ->execute();

            // select with where, order by and limit(pagination)
            $place_list = $place
                ->like([
                    'place.name' => '%name%',
                    'place.address' => 'place%',])
                ->orderBy([
                    'place.name' => 'desc',
                    'place.address' => 'desc',])
                ->limit(1,5)
                ->execute();

            // select with where, order by and limit(pagination)
            $waiter_list = $waiter
                ->like([
                    'waiter.name' => '%name%',
                    'restaurant.name' => 'restaurant%',])
                ->orderBy([
                    'restaurant.name' => 'desc',
                    'waiter.name' => 'desc',])
                ->limit(1,5)
                ->execute();

            // list of query's
            // Util::renderToJson($restaurant->dumpQuery());

            // render to json result
            Util::renderToJson([
                'restaurant_list' => $restaurant_list,
                'place_list' => $place_list,
                'waiter_list' => $waiter_list]);
        }
    }
}
