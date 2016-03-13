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
            $restaurant = new Restaurant($this->db_transaction);
            $waiter = new Waiter($this->db_transaction);
            $place = new Place($this->db_transaction);

            $table_page = Util::get($_GET,'table_id_page',1);

            $this->db_transaction->connect();

            // $restaurant->get(['restaurant.id' => 829]);

            $restaurant_list = $restaurant
                ->where([
                    'restaurant.serves_hot_dogs' => [1,0],])
                ->orderBy([
                    'restaurant.id' => 'desc'])
                ->limit($table_page,5)
                ->execute([
                    'join' => 'left']);

            $place_list = $place
                ->where()
                ->orderBy()
                ->limit(1,5)
                ->execute([
                    'join' => 'left']);

            // return Util::renderToJson($restaurant_list);

            $html_block = new HtmlBlock\HtmlBlock([
                'style' => 'padding-top: 50px;']);

            $html_block_nav = new HtmlBlock\Nav(
                $html_block,[
                    'id' => 'nav_id',
                    // 'class' => 'navbar navbar-inverse navbar-fixed-top',
                    'title' => 'titulo da bagaça trocar titulo',
                    // 'container_class' => 'col-md-12',
                    'model' => [
                        'menu 1' => 'http://williamborba.github.io/willer',
                        'menu 2' => 'http://williamborba.github.io/willer',
                        'menu 3' => 'http://williamborba.github.io/willer',
                        'menu 4' => 'http://williamborba.github.io/willer']]);

            $html_block_sidebar = new HtmlBlock\Sidebar(
                $html_block,[
                    'id' => 'sidebar_id',
                    'class' => '',
                    'style' => '',
                    // 'container_class' => 'col-md-2',
                    // 'container_style' => 'float:right;',
                    'title' => 'title do sidebar',
                    'text' => 'text do sidebar',
                    'footer' => 'foooter do sidebar',
                    'model' => [
                        'sidebar menu 1' => 'http://williamborba.github.io/willer',
                        'sidebar menu 2' => 'http://williamborba.github.io/willer',
                        'sidebar menu 3' => 'http://williamborba.github.io/willer']]);

            $html_block_sidebar_2 = new HtmlBlock\Sidebar(
                $html_block,[
                    'id' => 'sidebar_id',
                    'class' => '',
                    'style' => '',
                    // 'container_class' => 'col-md-2',
                    // 'container_style' => 'float:right;',
                    'title' => 'title do sidebar 2',
                    'text' => 'text do sidebar 2',
                    'footer' => 'foooter do sidebar 2',
                    'model' => [
                        'sidebar menu 1' => 'http://williamborba.github.io/willer',
                        'sidebar menu 2' => 'http://williamborba.github.io/willer',
                        'sidebar menu 3' => 'http://williamborba.github.io/willer']]);

            $html_block_page_header = new HtmlBlock\PageHeader(
                $html_block,[
                    'class' => '',
                    'style' => '',
                    'container_class' => 'col-md-12',
                    // 'container_style' => 'float:right;',
                    'title' => 'titulo da tabela',
                    'small_title' => ' titulo menor da tabela',]);

            $html_block_table = new HtmlBlock\Table(
                $html_block,[
                    'id' => 'table_id',
                    'style' => '',
                    'class' => '',
                    'container_class' => 'col-md-12',
                    'container_style' => 'float:right;',
                    'model' => $restaurant_list,
                    'title' => 'title do table',
                    'text' => 'text do table',
                    'footer' => 'foooter do table',
                    'label' => [
                        'id' => 'ID',
                        'name' => 'Nome',
                        'serves_hot_dogs' => 'Cachorro quente',
                        'serves_pizza' => 'Pizza',
                        'place_id' => [
                            'id' => 'ID',
                            'name' => 'Nome do lugar',
                            'address' => 'Endereço'
                        ]]]);

            $html_block_form = new HtmlBlock\Form(
                $html_block,[
                    'action' => 'restaurant/add',
                    'method' => 'post',
                    'id' =>  'form_id',
                    'style' => '',
                    'class' => '',
                    'title' => 'title do form',
                    'text' => 'text do form',
                    'footer' => 'foooter do form',
                    'container_class' => 'col-md-12',
                    // 'container_style' => 'float:left;',
                    'model' => $restaurant,
                    'label' => []]);

            $html = $html_block
                ->setHeadTitle('outro titulo')
                ->addCss('http://127.0.0.1/willer/willer/src/public/css/bootstrap.min.css')
                ->addCss('http://127.0.0.1/willer/willer/src/public/css/bootstrap-theme.min.css')
                ->addJs('https://code.jquery.com/jquery-2.2.1.min.js')
                ->addJs('http://127.0.0.1/willer/willer/src/public/js/bootstrap.min.js')
                ->appendBody($html_block_nav)
                ->appendBodyRow('col-md-2',[
                    $html_block_sidebar,
                    $html_block_sidebar_2])
                ->appendBodyRow('col-md-10',[
                    $html_block_page_header,
                    $html_block_table,
                    $html_block_form])
                ->renderHtml();

            print $html;
        }

        public function restaurantAdd() {
            // load model with Transaction instance
            $restaurant = new Restaurant($this->db_transaction);

            // open connection
            $this->db_transaction->connect();

            // get fields
            $name = Util::get($_POST,'name','place of test');
            $serves_hot_dogs = Util::get($_POST,'serves_hot_dogs',0);
            $serves_pizza = Util::get($_POST,'serves_pizza',0);

            // save
            $restaurant->save([
                'name' => $name,
                'serves_hot_dogs' => $serves_hot_dogs,
                'serves_pizza' => $serves_pizza,]);

            // $response = new Response();

            // return $response->code('200')->render([],'json');

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
