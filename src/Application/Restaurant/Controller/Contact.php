<?php

namespace Application\Restaurant\Controller {
    use Core\Controller;

    class Contact extends Controller {
        public function __construct($request_method = null) {
            parent::__construct($request_method);
        }

        public function contact() {
            print 'contact page';
        }
    }
}
