<?php

namespace App\Controller;

use Symfony\Component\Routing\Generator\UrlGenerator;

abstract class Controller
{
    /**
     * @var \Twig_Environment
     */
    private $twig;

    /**
     * @var UrlGenerator
     */
    private $urlGenerator;

    /**
     * @param \Twig_Environment $twig
     */
    public function __construct(\Twig_Environment $twig, UrlGenerator $urlGenerator)
    {
        $this->twig = $twig;
        $this->urlGenerator = $urlGenerator;
    }

    /**
     * @param string $templateName
     * @param array $params
     * @return string
     */
    public function render(string $templateName, array $params = []): string
    {
        return $this->twig->load($templateName)->render($params);
    }

    /**
     * @param string $routeName
     * @param array $params
     * @return string
     */
    public function generateUrl(string $routeName, array $params = []): string
    {
        return $this->urlGenerator->generate($routeName, $params);
    }
}
