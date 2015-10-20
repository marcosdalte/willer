<?php

require(ROOT_PATH.'/Core/Controller.php');
require(ROOT_PATH.'/Application/Restaurant/Controller/Home.php');

use \Application\Restaurant\Controller\Home;

class RestaurantTest extends PHPUnit_Framework_TestCase {
    public function testIndexWithRequestMethodIncorrect() {
        $_SERVER['REQUEST_METHOD'] = 'GET';

        $restaurant_home = new Home('GET');

        $result = $restaurant_home->index();

        $this->expectOutputString('ok');
        print $result;
    }
}

?>
