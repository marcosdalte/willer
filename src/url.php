<?php

$url = [
    '/^\/testphpinfo\/?$/' => ['Restaurant/Home/testPhpInfo',['GET']],
    '/^\/restaurant\/add\/?$/'        => ['Restaurant/Home/restaurantAdd',['GET']],
    '/^\/restaurant\/update\/?$/'     => ['Restaurant/Home/restaurantUpdate',['GET']],
    '/^\/restaurant\/delete\/?$/'     => ['Restaurant/Home/restaurantDelete',['GET']],
    '/^\/restaurant\/get\/?$/'        => ['Restaurant/Home/restaurantGet',['GET']],
    '/^\/restaurant\/select\/?$/'     => ['Restaurant/Home/restaurantSelect',['GET']],
    '/^\/restaurant\/other-page\/?$/' => ['Restaurant/Home/otherView',['GET']],
];
