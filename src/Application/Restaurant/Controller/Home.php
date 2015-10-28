<?php

namespace Application\Restaurant\Controller {
    use \Core\Controller;
    use \Application\Restaurant\Model\Restaurant;

    class Home extends Controller {
        public function __construct($request_method = null) {
            parent::__construct($request_method);
        }

        public function index() {
        	$restaurant = new Restaurant;

            print 'ok';
        }
    }
}
