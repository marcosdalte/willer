<?php

namespace Application\Restaurant\Controller {
    use \Core\Controller;

    class Home extends Controller {
        public function __construct($request_method = null) {
            parent::__construct($request_method);
        }

        public function index() {
            print 'ok';
        }
    }
}
