<?php

namespace Application\Restaurant {
    class Url {
        static public function url() {
            return [
                '/cadastro/produto/{var1:[0-9]}/{var2:[a-z]+}/{var3:[0-9]+}' => ['Home\test',['GET','POST'],'home_default'],
                '/'           => ['Home\home',['GET'],'home_defaul'],
                '/home'       => ['Home\home',['GET'],'home_home'],
                '/contact'    => ['Contact\contact',['POST'],'contact_defaul'],
                '/restaurant' => ['Home\Dispath',['GET','POST','PUT','DELETE'],'restaurant_dispath'],
            ];
        }
    }
}
