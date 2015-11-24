<?php

use \Core\DAO\Transaction;
use \Application\Restaurant\Model\Restaurant;

class RestaurantCrudTest extends PHPUnit_Framework_TestCase {
    public function testRestaurantCrudAdd() {
        // load transaction object
        $db_transaction = new Transaction();

        // load model with Transaction instance
        $restaurant = new Restaurant($db_transaction);

        // open connection
        $db_transaction->connect();

        // save
        $restaurant->save([
            'place' => 'place of test',
            'serves_hot_dogs' => 1,
            'serves_pizza' => 1,]);

        // create restaurant test object
        $restaurant_test = new Restaurant();

        // set data
        $restaurant_test->id = $restaurant->id;
        $restaurant_test->place = 'place of test';
        $restaurant_test->serves_hot_dogs = 1;
        $restaurant_test->serves_pizza = 1;

        // compare
        $this->assertEquals(print_r($restaurant_test,true),print_r($restaurant,true));
    }

    public function testRestaurantCrudUpdate() {
        // load transaction object
        $db_transaction = new Transaction();

        // load model with Transaction instance
        $restaurant = new Restaurant($db_transaction);

        // open connection
        $db_transaction->connect();

        // save
        $restaurant->save([
            'place' => 'place of test',
            'serves_hot_dogs' => 1,
            'serves_pizza' => 1,]);

        // update
        $restaurant->place = 'bla e bla';
        $restaurant->serves_hot_dogs = 0;
        $restaurant->serves_pizza = 0;
        $restaurant->save();

        // create restaurant test object
        $restaurant_test = new Restaurant();

        // set data
        $restaurant_test->id = $restaurant->id;
        $restaurant_test->place = 'bla e bla';
        $restaurant_test->serves_hot_dogs = 0;
        $restaurant_test->serves_pizza = 0;

        // compare
        $this->assertEquals(print_r($restaurant_test,true),print_r($restaurant,true));
    }

    public function testRestaurantCrudDelete() {
        // load transaction object
        $db_transaction = new Transaction();

        // load model with Transaction instance
        $restaurant = new Restaurant($db_transaction);

        // open connection
        $db_transaction->connect();

        // save
        $restaurant->save([
            'place' => 'place of test',
            'serves_hot_dogs' => 1,
            'serves_pizza' => 1,]);

        // delete register
        $restaurant->delete();

        // compare
        $this->assertNull($restaurant->id);
        $this->assertNull($restaurant->place);
        $this->assertNull($restaurant->serves_hot_dogs);
        $this->assertNull($restaurant->serves_pizza);
    }
}

?>