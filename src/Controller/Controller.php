<?php

namespace App\Controller;

use Gumlet\ImageResize;
use Gumlet\ImageResizeException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Generator\UrlGenerator;
use Twig_Environment;

abstract class Controller
{
    const IMAGE_EXTENSIONS_WHITELIST = ['jpg', 'jpeg', 'png'];

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

    /**
     * @param string $fileName
     * @return string
     */
    protected function getFilePath(string $fileName): string
    {
        return $_SERVER['DOCUMENT_ROOT'] . "fileStorage/" . $fileName;
    }

    /**
     * @return string
     */
    protected function getImageDir(): string
    {
        return $_SERVER['DOCUMENT_ROOT'] . "public/uploaded/";
    }

    /**
     * @param UploadedFile $file
     * @return bool|string
     */
    protected function saveFile(UploadedFile $file)
    {
        $extension = $file->guessExtension();
        if (!in_array($extension, self::IMAGE_EXTENSIONS_WHITELIST)) {
            return false;
        }

        $imageResize = new ImageResize($file->getPathname());
        $imageResize->resize(300, 300, true);
        try {
            $imageResize->save($file->getPathname());
        } catch (ImageResizeException $e) {
            return false;
        }

        $imageName = uniqid() . '.' . $extension;
        $file->move($this->getImageDir(), $imageName);

        return $imageName;
    }
}
