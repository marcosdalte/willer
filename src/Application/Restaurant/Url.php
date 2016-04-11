<?php

namespace Application\Restaurant {
    class Url {
        static public function url() {
            return [
                '/cadastro/produto/{var1:[0-9]}/{var2:[a-z]+}/{var3:[0-9]+}' => ['Home\test',['GET','POST'],'test_id'],
                '/'           => ['Home\home',['GET'],'home_default'],
                '/home'       => ['Home\home',['GET'],'home_home'],
                '/contact'    => ['Contact\contact',['POST'],'contact_default'],
                '/restaurant/add'    => ['Home\restaurantAdd',['POST'],'restaurant_add'],
                '/restaurant' => ['Home\Dispath',['GET','POST','PUT','DELETE'],'restaurant_dispath'],
            ];
        }
    }
}
