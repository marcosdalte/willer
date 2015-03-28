<?php
/*urls*/
$URL = [
    "/^\/?$/"        => ["controller" => "Documentation/Home/index","request_method" => ["GET","POST"]],
    "/^home\/?$/"    => ["controller" => "Documentation/Home/index","request_method" => "GET"],
    "/^contato\/?$/" => ["controller" => "Documentation/Home/contact","request_method" => "GET"],
];