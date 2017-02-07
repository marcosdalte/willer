<?php

namespace Application\MyApp\Controller {
    use Core\{Controller,Request,Response};
    use Core\Exception\WException;
    use Core\DAO\Transaction;
    use Component\HtmlBlock;
    use \Exception as Exception;

    class Test extends Controller {
        public function __construct(Request $request) {
            parent::__construct($request);
        }

        public function index() {
            // content
        }
    }
}
