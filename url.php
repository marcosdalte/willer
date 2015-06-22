<?php
/*urls*/
$URL = [
    "/^\/?$/"        => ["Test/Home/index",["GET","POST"]],
    "/^home\/?$/"    => ["Test/Home/index","GET"],
    "/^test\/?$/"    => ["Test/Home/test",null],
    "/^tpl\/([1-9]+)\/?$/"     => ["Test/Home/tpl",null],
];
