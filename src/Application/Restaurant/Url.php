<?php

namespace Application\Restaurant {
    class Url {
        static public function url() {
            return [
                '/^\/?$/'                         => ["Restaurant/Home/home",['GET']],
                '/^\/home\/?$/'                   => ["Restaurant/Home/home",['GET']],
                '/^\/contact\/?$/'                => ["Restaurant/Contact/contact",['GET']],
                '/^\/restaurant\/add\/?$/'        => ['Restaurant/Home/restaurantAdd',['GET']],
                '/^\/restaurant\/update\/?$/'     => ['Restaurant/Home/restaurantUpdate',['GET']],
                '/^\/restaurant\/delete\/?$/'     => ['Restaurant/Home/restaurantDelete',['GET']],
                '/^\/restaurant\/get\/?$/'        => ['Restaurant/Home/restaurantGet',['GET']],
                '/^\/restaurant\/select\/?$/'     => ['Restaurant/Home/restaurantSelect',['GET']],
                '/^\/restaurant\/other-page\/?$/' => ['Restaurant/Home/otherView',['GET']],
            ];
        }
    }
}
