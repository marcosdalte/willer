<?php

namespace Application\MyApp {
    class Url {
        static public function url() {
            $url = [
                '/'                           => ['Test\index',['GET'],'myapp_test_id_of_url'],
                '/page/test'                  => ['Test\index',['GET'],'id_is_unique'],
                '/page/test/{test_id:[0-9]+}' => ['Test\index',['GET'],'id_of_this_url'],
            ];

            return $url;
        }
    }
}
