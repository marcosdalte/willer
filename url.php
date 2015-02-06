<?php
/*urls*/
$URL = [
    "/^\/?$/"        => ["controller" => "IDoc/Home/index","request_method" => ["GET","POST"]],
    "/^home\/?$/"    => ["controller" => "IDoc/Home/index","request_method" => "GET"],
    "/^contato\/?$/" => ["controller" => "IDoc/Home/contact","request_method" => "GET"],
];