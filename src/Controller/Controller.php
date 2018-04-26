<?php

namespace App\Controller;

abstract class Controller
{
    /**
     * @var \Twig_Environment
     */
    private $twig;

    /**
     * @param \Twig_Environment $twig
     */
    public function __construct(\Twig_Environment $twig)
    {
        $this->twig = $twig;
    }

    /**
     * @param string $templateName
     * @param array $params
     * @return string
     */
    public function render(string $templateName, array $params = []) : string
    {
        return $this->twig->load($templateName)->render($params);
    }
}
