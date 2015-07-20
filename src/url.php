<?php

$url = [
	"/^\/?$/" => "Test/Home/test",
	"/^test\/?$/" => "Test/Home/test",
    "/^home\/?$/" => "Test/Home/index",
    "/^tpl\/([1-9]+)\/([a-z]+)\/?$/"  => "Test/Home/tpl",
];
