<?php

namespace App\Controller;


use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ArticlesController extends Controller
{
    public function getArticlesAction(Request $request) : Response
    {
        return new Response($this->render('index.html.twig'));
    }

    public function addArticleAction(Request $request) : Response
    {

    }
}
