<?php
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

require __DIR__.'/../vendor/autoload.php';

$routesParameters = [
    [
        'name' => 'get-articles',
        'url' => '/article',
        'controller' => 'ArticleController',
        'action' => 'getArticles',
        'method' => 'GET'
    ],
    [
        'name' => 'add-articles',
        'url' => '/article/add',
        'controller' => 'ArticleController',
        'action' => 'addArticle',
        'method' => 'POST'
    ]
];

$routes = new RouteCollection();
foreach ($routesParameters as $routeParameters) {
    //$routes[] = new Route();
}
