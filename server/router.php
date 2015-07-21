<?php

$extension_static = ['png','jpg','jpeg','gif','css','js'];

$extension = pathinfo($_SERVER['REQUEST_URI'],PATHINFO_EXTENSION);

if (in_array($extension,$extension_static)) {
    return false;
}

require ("src/bootstrap.php");