<?php

define('BP', __DIR__);
define('DS', DIRECTORY_SEPARATOR);

require_once BP . DS . 'lib' . DS . 'Autoloader.php';
$autoLoader = new \Autoloader();
$autoLoader->register();

$autoLoader->addNamespace('Http', BP . DS . 'lib' . DS . 'Http');
$autoLoader->addNamespace('Html', BP . DS . 'lib' . DS . 'Html');
$autoLoader->addNamespace('Filesystem', BP . DS . 'lib' . DS . 'Filesystem');
$autoLoader->addNamespace('Controller', BP . DS . 'src' . DS . 'Controller');

$route = new \Http\Router();
$route->dispatch();
