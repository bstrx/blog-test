<?php

use App\Kernel;
use Symfony\Component\HttpFoundation\Request;

require __DIR__.'/../vendor/autoload.php';

$_SERVER['DOCUMENT_ROOT'] = dirname(__FILE__) . '/../';

$kernel = new Kernel();
$response = $kernel->runApp(Request::createFromGlobals());
$response->send();
