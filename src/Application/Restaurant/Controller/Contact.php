<?php

namespace Application\Restaurant\Controller {
    use Core\Controller;
    use Core\DAO\Transaction;
    use Core\Util;
    use Application\Restaurant\Model\Restaurant;

    class Contact extends Controller {
        public function __construct($request_method = null) {
            parent::__construct($request_method);
        }

        public function contact() {
            print 'contact page';
        }
    }
}
