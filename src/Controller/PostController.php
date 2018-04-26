<?php

namespace App\Controller;

use App\Entity\Post;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class PostController extends Controller
{
    /**
     * @return Response
     */
    public function getPostsAction() : Response
    {
        $postsInfo = [
            [
                'title' => 'My latest blog post! Sorry, no picture this time!',
                'time' => '01/11/2011',
                'content' => 'Today awesome stuff happened to me, I\'ll tell you guys all about it! It\'s going to be an amazing story,
                    I can\'t wait to tell you, but since this is a fake site you\'ll never know!'
            ],
            [
                'title' => '2 My latest blog post! Sorry, no picture this time!',
                'time' => '01/11/2011',
                'content' => 'Today awesome stuff happened to me, I\'ll tell you guys all about it! It\'s going to be an amazing story,
                    I can\'t wait to tell you, but since this is a fake site you\'ll never know!'
            ]
        ];

        //TODO read from file

        $posts = [];
        foreach ($postsInfo as $postInfo) {
            $post = new Post();
            $post->setTime($postInfo['time']);
            $post->setTitle($postInfo['title']);
            $post->setContent($postInfo['content']);

            $posts[] = $post;
        }

        $mostUsedWords = ['First', 'Second', 'Third', 'Fourth', 'Five'];

        return new Response($this->render('index.html.twig', [
            'posts' => $posts,
            'mostUsedWords' => $mostUsedWords,
            'addPostUrl' => $this->generateUrl('add-article')
        ]));
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function addPostAction(Request $request) : Response
    {
        //TODO add validation
        //TODO save to file

        $post = new Post();
        $post->setTime($request->get('time'));
        $post->setTitle($request->get('title'));
        $post->setContent($request->get('content'));

        file_put_contents($_SERVER['DOCUMENT_ROOT'] . "fileStorage/posts.txt", serialize($post));

        $serializedPost = file_get_contents($_SERVER['DOCUMENT_ROOT'] . "fileStorage/posts.txt");
        print_r(unserialize($serializedPost));



        return new Response($this->render('post.html.twig', ['post' => $post]));
    }
}
