<?php
/*urls*/
$URL = [
	"/^\/?$/" => "Test/Home/test",
    // "/^\/?$/"        => ["Test/Home/index",["GET","POST"]],
    "/^home\/?$/" => "Test/Home/index",
    "/^test\/?$/" => "Test/Home/test",
    "/^tpl\/([1-9]+)\/([a-z]+)?$/"  => "Test/Home/tpl",
];
