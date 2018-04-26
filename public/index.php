<?php

use App\Kernel;
use Symfony\Component\HttpFoundation\Request;

require __DIR__.'/../vendor/autoload.php';

$kernel = new Kernel();
$response = $kernel->runApp(Request::createFromGlobals());
$response->send();
