<?php

namespace App\Controller;

use App\Entity\Post;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class PostController extends Controller
{
    const USER_EMAIL = 'vladimir.prudilin@opensoftdev.ru';

    /**
     * @return Response
     */
    public function getPostsAction(): Response
    {
        //TODO remove
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

        return new Response($this->render('index.html.twig', [
            'posts' => $this->getPosts(),
            'mostUsedWords' => $this->getMostUsedWords(),
            'addPostUrl' => $this->generateUrl('add-article')
        ]));
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function addPostAction(Request $request): Response
    {
        $errors = $this->validateAddPost($request);
        if ($errors) {
            return $this->getJsonResponse(null, $errors, 422);
        }

        $post = $this->createPostFromRequest($request);
        $posts = $this->getPosts();
        $posts[] = $post;
        $this->putPosts($posts);
        $this->updateMostUsedWords($posts);

        return $this->getJsonResponse($this->render('post.html.twig', ['post' => $post]));
    }

    /**
     * @param Request $request
     * @return Post
     */
    private function createPostFromRequest(Request $request): Post
    {
        $post = new Post();
        $post->setTime(new \DateTime());
        $post->setTitle($request->get('title'));
        $post->setContent($request->get('content'));

        /** @var UploadedFile $file */
        $file = $request->files->get('image');
        if ($file) {
            $savedFileName = $this->saveFile($file);
            if ($savedFileName) {
                $post->setImage($savedFileName);
            }
        }

        return $post;
    }


    /**
     * @param Request $request
     * @return array
     */
    private function validateAddPost(Request $request): array
    {
        $errors = [];
        if ($request->get('email') !== self::USER_EMAIL) {
            $errors[] = "Your email doesn't match our secret email";
        }

        if (false) { //TODO CSRF maybe?
            $errors[] = "Request can't be trusted";
        }

        return $errors;
    }

    /**
     * @return Post[]
     */
    private function getPosts()
    {
        $serializedPosts = file_get_contents($this->getFilePath('posts.txt'));
        if ($serializedPosts) {
            $posts = array_reverse(unserialize($serializedPosts));
        } else {
            $posts = [];
        }

        return $posts;
    }

    /**
     * @param Post[] $posts
     * @return bool|int
     */
    private function putPosts(array $posts)
    {
        return file_put_contents($this->getFilePath('posts.txt'), serialize($posts));
    }

    /**
     * @return string[]
     */
    private function getMostUsedWords()
    {
        return unserialize(file_get_contents($this->getFilePath('mostUsedWords.txt')));
    }

    /**
     * @param Post[] $posts
     * @return array
     */
    private function countMostUsedWords(array $posts)
    {
        $allContent = array_reduce($posts, function ($carry, Post $post) {
            $carry .= ' ' . $post->getContent();

            return $carry;
        });

        $words = array_count_values(str_word_count(strtolower($allContent), 1));
        arsort($words);

        $words = array_filter($words, function ($word) {
            return strlen($word) > 4;
        }, ARRAY_FILTER_USE_KEY);

        return array_slice(array_keys($words), 0, 5);
    }

    /**
     * @param Post[] $posts
     * @return bool|int
     */
    private function updateMostUsedWords(array $posts)
    {
        $mostUsedWords = $this->countMostUsedWords($posts);

        return file_put_contents($this->getFilePath('mostUsedWords.txt'), serialize($mostUsedWords));
    }
}
