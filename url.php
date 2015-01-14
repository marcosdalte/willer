<?php
/*urls*/
$URL = [
    "/^\/?$/"     => ["controller" => "IDoc\Home","rest_rule" => ["GET","POST"],"protect_resource" => false],
    "/^home\/?$/" => ["controller" => "IDoc\Home","rest_rule" => ["GET","POST"],"protect_resource" => false],
];