<?php

namespace App;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

class Kernel
{
    private const CONTROLLER_NAMESPACE = '\App\Controller\\';
    private const CONTROLLER_POSTFIX = 'Controller';
    private const ACTION_POSTFIX = 'Action';

    /**
     * @param Request $request
     * @return Response
     */
    public function runApp(Request $request) : Response
    {
        $parameters = $this->getParameters($this->getRoutes(), $request->getPathInfo());

        $controllerFullName = self::CONTROLLER_NAMESPACE . $parameters['controller'] . self::CONTROLLER_POSTFIX;
        $controller = new $controllerFullName($this->getTwig());

        $actionName = $parameters['action'] . self::ACTION_POSTFIX;

        return $controller->$actionName($request);
    }

    /**
     * @param RouteCollection $routes
     * @param string $path
     * @return array
     */
    private function getParameters(RouteCollection $routes, string $path) : array
    {
        $context = new RequestContext('/');
        $matcher = new UrlMatcher($routes, $context);

        return $matcher->match($path);
    }

    /**
     * @return \Twig_Environment
     */
    private function getTwig() : \Twig_Environment
    {
        $loader = new \Twig_Loader_Filesystem('../templates');

        return $twig = new \Twig_Environment($loader);
    }

    /**
     * @return RouteCollection
     */
    private function getRoutes() : RouteCollection
    {
        $routesParameters = [
            [
                'name' => 'main-page',
                'path' => '/',
                'controller' => 'Articles',
                'action' => 'getArticles',
                'method' => 'GET'
            ],
            [
                'name' => 'add-article',
                'path' => '/article/add',
                'controller' => 'Articles',
                'action' => 'addArticle',
                'method' => 'POST'
            ]
        ];

        $routes = new RouteCollection();
        foreach ($routesParameters as $routeParameters) {
            $route = new Route(
                $routeParameters['path'],
                [
                    'controller' => $routeParameters['controller'],
                    'action' => $routeParameters['action']
                ]
            );

            $route->setMethods([$routeParameters['method']]);
            $routes->add($routeParameters['name'], $route);
        }

        return $routes;
    }

}
