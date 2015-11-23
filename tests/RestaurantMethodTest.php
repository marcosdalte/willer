<?php

use \Application\Restaurant\Controller\Home;

class RestaurantMethodTest extends PHPUnit_Framework_TestCase {
    public function testExceptionRequestMethodInvalid() {
        $this->setExpectedException('Core\Exception\WF_Exception');

        $_SERVER['REQUEST_METHOD'] = 'GET';

        $restaurant_controller = new Home('POST');

        $restaurant_controller->requestMethodGetTest();
    }

    public function testRequestMethodValid() {
        $this->expectOutputString('ok');

        $_SERVER['REQUEST_METHOD'] = 'POST';

        $restaurant_controller = new Home('POST');

        $restaurant_controller->requestMethodGetTest();
    }
}

?>