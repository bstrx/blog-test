<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Generator\UrlGenerator;
use Twig_Environment;

abstract class Controller
{
    /**
     * @var Twig_Environment
     */
    private $twig;

    /**
     * @var UrlGenerator
     */
    private $urlGenerator;

    /**
     * @param Twig_Environment $twig
     * @param UrlGenerator $urlGenerator
     */
    public function __construct(Twig_Environment $twig, UrlGenerator $urlGenerator)
    {
        $this->twig = $twig;
        $this->urlGenerator = $urlGenerator;
    }

    /**
     * @param string $templateName
     * @param array $params
     * @return string
     */
    protected function render(string $templateName, array $params = []): string
    {
        return $this->twig->load($templateName)->render($params);
    }

    /**
     * @param string $routeName
     * @param array $params
     * @return string
     */
    protected function generateUrl(string $routeName, array $params = []): string
    {
        return $this->urlGenerator->generate($routeName, $params);
    }

    /**
     * @param mixed|null $data
     * @param array $errors
     * @param int $status
     * @return JsonResponse
     */
    protected function getJsonResponse($data = null, array $errors = [], $status = 200): JsonResponse
    {
        $content = [
            'data' => $data,
            'errors' => $errors
        ];

        return new JsonResponse(json_encode($content), $status);
    }
}
