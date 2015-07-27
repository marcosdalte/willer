<?php

$url = [
	'/^\/?$/' 						   => ['Test/Home/index',['GET']],
	'/^test\/?$/' 					   => ['Test/Home/test','GET'],
    '/^home\/?$/' 					   => ['Test/Home/index',null],
    '/^about\/([1-9]+)\/([a-z]+)\/?$/' => ['Test/Home/about',['POST','GET']],
];
