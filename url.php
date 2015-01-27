<?php
/*urls*/
$URL = [
    "/^\/?$/"        => ["controller" => "IDoc\Home\index","rest_rule" => ["GET","POST"],"protect_resource" => false],
    "/^home\/?$/"    => ["controller" => "IDoc\Home\index","rest_rule" => ["GET","POST"],"protect_resource" => false],
    "/^contato\/?$/" => ["controller" => "IDoc\Home\contact","rest_rule" => ["GET","POST"],"protect_resource" => false],
];