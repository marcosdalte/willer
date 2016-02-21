<?php

define('DEBUG',true);
define('URL_PREFIX','/');
define('REQUEST_URI','/');
define('ROOT_PATH',dirname(__DIR__));
define('DATABASE_PATH',ROOT_PATH.'/src/Config/database.json');
define('DATABASE','default');

require(ROOT_PATH.'/vendor/wborba/willer-core/src/Core/Exception/WF_Exception.php');
require(ROOT_PATH.'/vendor/wborba/willer-core/src/Core/Util.php');
require(ROOT_PATH.'/vendor/wborba/willer-core/src/Core/Controller.php');
require(ROOT_PATH.'/vendor/wborba/willer-core/src/Core/DAO/Transaction.php');
require(ROOT_PATH.'/src/Application/Restaurant/Controller/Home.php');
require(ROOT_PATH.'/vendor/wborba/willer-core/src/Core/DAO/DataManipulationLanguage.php');
require(ROOT_PATH.'/vendor/wborba/willer-core/src/Core/Model.php');
require(ROOT_PATH.'/src/Application/Restaurant/Model/Restaurant.php');
require(ROOT_PATH.'/src/Application/Restaurant/Model/Waiter.php');
require(ROOT_PATH.'/src/Application/Restaurant/Model/Place.php');