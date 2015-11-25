<?php

use \Core\DAO\Transaction;
use \Application\Restaurant\Model\Restaurant;
use \Application\Restaurant\Model\Waiter;
use \Application\Restaurant\Model\Place;

class RestaurantCrudRelationTest extends PHPUnit_Framework_TestCase {
    public function testRestaurantCrudRelationAdd() {
        // load transaction object
        $transaction = new Transaction();

        // load model with Transaction instance
        $restaurant = new Restaurant($transaction);
        $place = new Place($transaction);

        // open connection
        $transaction->connect();

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

        // create test object
        $place_test = new Place();
        $restaurant_test = new Restaurant();

        // set data in place_test
        $place_test->id = $place->id;
        $place_test->name = 'place name test';
        $place_test->address = 'place address test';

        // set data in restaurant_test
        $restaurant_test->id = $restaurant->id;
        $restaurant_test->place_id = $place_test;
        $restaurant_test->name = 'restaurant name test';
        $restaurant_test->serves_hot_dogs = 1;
        $restaurant_test->serves_pizza = 1;

        // compare objects
        $this->assertEquals(print_r($restaurant_test,true),print_r($restaurant,true));
    }

    // public function testRestaurantCrudRelationUpdate() {
    //     // load transaction object
    //     $transaction = new Transaction();

    //     // load model with Transaction instance
    //     $restaurant = new Restaurant($transaction);

    //     // open connection
    //     $transaction->connect();

    //     // save
    //     $restaurant->save([
    //         'place' => 'place of test',
    //         'serves_hot_dogs' => 1,
    //         'serves_pizza' => 1,]);

    //     // update
    //     $restaurant->place = 'bla e bla';
    //     $restaurant->serves_hot_dogs = 0;
    //     $restaurant->serves_pizza = 0;
    //     $restaurant->save();

    //     // create restaurant test object
    //     $restaurant_test = new Restaurant();

    //     // set data
    //     $restaurant_test->id = $restaurant->id;
    //     $restaurant_test->place = 'bla e bla';
    //     $restaurant_test->serves_hot_dogs = 0;
    //     $restaurant_test->serves_pizza = 0;

    //     // compare
    //     $this->assertEquals(print_r($restaurant_test,true),print_r($restaurant,true));
    // }

    // public function testRestaurantCrudRelationDelete() {
    //     // load transaction object
    //     $transaction = new Transaction();

    //     // load model with Transaction instance
    //     $restaurant = new Restaurant($transaction);

    //     // open connection
    //     $transaction->connect();

    //     // save
    //     $restaurant->save([
    //         'place' => 'place of test',
    //         'serves_hot_dogs' => 1,
    //         'serves_pizza' => 1,]);

    //     // delete register
    //     $restaurant->delete();

    //     // compare
    //     $this->assertNull($restaurant->id);
    //     $this->assertNull($restaurant->place);
    //     $this->assertNull($restaurant->serves_hot_dogs);
    //     $this->assertNull($restaurant->serves_pizza);
    // }

    // public function testRestaurantCrudRelationGet() {
    //     // load transaction object
    //     $transaction = new Transaction();

    //     // load model with Transaction instance
    //     $restaurant = new Restaurant($transaction);

    //     // open connection
    //     $transaction->connect();

    //     // delete if exists
    //     $restaurant->delete([
    //         'place' => 'place_of_test_unique']);

    //     // save
    //     $restaurant->save([
    //         'place' => 'place_of_test_unique',
    //         'serves_hot_dogs' => 1,
    //         'serves_pizza' => 1,]);

    //     // get unique register
    //     $restaurant->get([
    //         'place' => 'place_of_test_unique']);

    //     // compare
    //     $this->assertEquals($restaurant->place,'place_of_test_unique');
    // }

    // public function testRestaurantCrudRelationSelect() {
    //     // load transaction object
    //     $transaction = new Transaction();

    //     // load model with Transaction instance
    //     $restaurant = new Restaurant($transaction);

    //     // open connection
    //     $transaction->connect();

    //     // delete if exists
    //     $restaurant->delete();

    //     // save
    //     $restaurant->save([
    //         'place' => 'place of test',
    //         'serves_hot_dogs' => 1,
    //         'serves_pizza' => 1,]);

    //     // select with where, order by and limit(pagination)
    //     $restaurant_list = $restaurant
    //         ->where([
    //             'restaurant.serves_hot_dogs' => [0,1],
    //             'restaurant.serves_pizza' => [0,1],])
    //         ->orderBy([
    //             'restaurant.place' => 'desc'])
    //         ->limit(1,5)
    //         ->execute();

    //     // compare
    //     $this->assertNotEmpty($restaurant_list);
    // }
}

?>