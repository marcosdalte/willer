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

        // delete all register's in restaurant table
        $restaurant->delete();

        // save
        $restaurant->save([
            'place' => 'place of test',
            'serves_hot_dogs' => 1,
            'serves_pizza' => 1,]);

        $obj = New stdClass();
        $obj->id = 1;
        $obj->place = 'place of test';
        $obj->serves_hot_dogs = 1;
        $obj->serves_pizza = 1;

        $this->assertEquals($obj,$restaurant);
    }
}

?>