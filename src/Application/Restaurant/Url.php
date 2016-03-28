<?php

namespace Application\Restaurant {
    class Url {
        static public function url() {
            return [
                '/'           => ['Home:home',['GET'],'home_defaul'],
                '/home'       => ['Home:home',['GET'],'home_home'],
                '/contact'    => ['Contact:contact',['POST'],'contact_defaul'],
                '/restaurant' => ['Home:Dispath',['GET','POST','PUT','DELETE'],'restaurant_dispath'],
            ];

            // return [
            //     '/cadastro/produto/{var1:[0-9]}/{var2:[a-z]+}/{var3:[0-9]+}' => ['Home:test',['GET'],'route_id'],
            // ];

            // return [
            //     '/'                               => ['Dashboard\index',['GET'],'id_route1'],
            //     '/dashboard/'                     => ['Dashboard\index',['GET'],'id_route2'],
            //     '/cadastro/produto/{name}/{phone}/' => ['Register\product',['GET'],'id_route3'],
            //     '/cadastro/produto2/(.*)/(.*)/'   => ['Register\product',['GET'],'id_route4'],
            // ];
        }
    }
}
