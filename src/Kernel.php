<?php

namespace App;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGenerator;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;
use Twig_Environment;
use Twig_Loader_Filesystem;

class Kernel
{
    private const CONTROLLER_NAMESPACE = '\App\Controller\\';
    private const CONTROLLER_POSTFIX = 'Controller';
    private const ACTION_POSTFIX = 'Action';

    /**
     * Turn request into response by calling an action of a controller
     *
     * @param Request $request
     * @return Response
     */
    public function runApp(Request $request): Response
    {
        $routeParams = $this->getParameters($this->getRoutes(), $request->getPathInfo());

        $controllerFullName = self::CONTROLLER_NAMESPACE . $routeParams['controller'] . self::CONTROLLER_POSTFIX;
        $controller = new $controllerFullName(
            $this->getTwig(),
            $this->getUrlGenerator()
        );

        $actionName = $routeParams['action'] . self::ACTION_POSTFIX;

        return $controller->$actionName($request);
    }

    /**
     * Return a route with all info depending on a requested url
     *
     * @param RouteCollection $routes
     * @param string $path
     * @return array
     */
    private function getParameters(RouteCollection $routes, string $path): array
    {
        $context = new RequestContext('/');
        $matcher = new UrlMatcher($routes, $context);

        return $matcher->match($path);
    }

    /**
     * Return Twig template engine
     *
     * @return Twig_Environment
     */
    private function getTwig(): Twig_Environment
    {
        $loader = new Twig_Loader_Filesystem('../templates');

        return $twig = new Twig_Environment($loader);
    }

    /**
     * Return collection of routes.
     * TODO Move to router and configs
     *
     * @return RouteCollection
     */
    private function getRoutes(): RouteCollection
    {
        $routesParameters = [
            [
                'name' => 'main-page',
                'path' => '/',
                'controller' => 'Post',
                'action' => 'getPosts'
            ],
            [
                'name' => 'add-article',
                'path' => '/article/add',
                'controller' => 'Post',
                'action' => 'addPost'
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

            $routes->add($routeParameters['name'], $route);
        }

        return $routes;
    }

    /**
     * @return UrlGenerator
     */
    private function getUrlGenerator(): UrlGenerator
    {
        return new UrlGenerator($this->getRoutes(), new RequestContext(''));
    }
}
