<?php

require(ROOT_PATH.'/Core/Exception/WF_Exception.php');
require(ROOT_PATH.'/Core/Util.php');
require(ROOT_PATH.'/Core/Controller.php');
require(ROOT_PATH.'/Core/DAO/Transaction.php');
require(ROOT_PATH.'/Application/Restaurant/Controller/Home.php');

use \Application\Restaurant\Controller\Home;

class RestaurantTest extends PHPUnit_Framework_TestCase {
    public function testExceptionRequestMethodInvalid() {
        $this->setExpectedException('Core\Exception\WF_Exception');

        $_SERVER['REQUEST_METHOD'] = 'GET';

        $restaurant_controller = new Home('POST');

        $restaurant_controller->requestMethodTest();
    }

    public function testRequestMethodValid() {
        $this->expectOutputString('ok');

        $_SERVER['REQUEST_METHOD'] = 'POST';

        $restaurant_controller = new Home('POST');

        $restaurant_controller->requestMethodTest();
    }
}

?>
