<?php

require(ROOT_PATH.'/Core/Exception/WF_requestMethodInvalid.php');
require(ROOT_PATH.'/Core/Util.php');
require(ROOT_PATH.'/Core/Controller.php');
require(ROOT_PATH.'/Application/Restaurant/Controller/Home.php');

use \Application\Restaurant\Controller\Home;

class RestaurantTest extends PHPUnit_Framework_TestCase {
    public function testExceptionRequestMethodInvalid() {
        $this->setExpectedException('Core\Exception\WF_requestMethodInvalid','WF_requestMethodInvalid');

        $_SERVER['REQUEST_METHOD'] = 'GET';

        $restaurant_home = new Home('POST');

        $restaurant_home->index();
    }
}

?>
