<?php

$url = [
	'/^\/?$/' 						 => ['Test/Home/test',['GET']],
	'/^test\/?$/' 					 => ['Test/Home/test','GET'],
    '/^home\/?$/' 					 => ['Test/Home/index',null],
    '/^tpl\/([1-9]+)\/([a-z]+)\/?$/' => ['Test/Home/tpl',['POST','GET']],
];
