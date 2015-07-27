<?php

namespace Application\Test\Controller {
    use \Exception as Exception;
    use \Core\Controller;

    class Home extends Controller {
        public function __construct($request_method = null) {
            parent::__construct($request_method);
        }

        public function index() {
            print 'ok, success!';
        }

        public function test() {
            print 'ok, test success!';
        }

        public function about($var1,$var2) {
            $loader = new \Twig_Loader_Filesystem(ROOT_PATH.'/Application/Test/view');

            $twig = new \Twig_Environment($loader,[
                // "cache" => ROOT_PATH.'/Application/Test/view',
            ]);

            print $twig->render("home.html",[
                "var1" => $var1,
                "var2" => $var2]);
        }
    }
}
