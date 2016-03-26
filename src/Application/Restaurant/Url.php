<?php

namespace Application\Restaurant {
    class Url {
        static public function url() {
            return [
                '/^\/?$/'                            => ['Restaurant/Home/home',['GET']],
                '/^\/home\/?$/'                      => ['Restaurant/Home/home',['GET']],
                '/^\/contact\/?$/'                   => ['Restaurant/Contact/contact',['POST']],
                '/^\/restaurant\/add\/?$/'           => ['Restaurant/Home/restaurantAdd',['GET','POST']],
                '/^\/restaurant\/update\/?$/'        => ['Restaurant/Home/restaurantUpdate',['GET']],
                '/^\/restaurant\/delete\/?$/'        => ['Restaurant/Home/restaurantDelete',['GET']],
                '/^\/restaurant\/get\/?$/'           => ['Restaurant/Home/restaurantGet',['GET']],
                '/^\/restaurant\/listing\/?$/'       => ['Restaurant/Home/restaurantListing',['GET']],
                '/^\/restaurant\/like\/listing\/?$/' => ['Restaurant/Home/restaurantLikeListing',['GET']],
                '/^\/restaurant\/other-page\/?$/'    => ['Restaurant/Home/otherView',['GET']],
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
