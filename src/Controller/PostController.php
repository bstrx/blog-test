<?php

namespace App\Controller;

use App\Entity\Post;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class PostController extends Controller
{
    /**
     * TODO: Move these to some config
     */
    private const USER_EMAIL = 'bstrxx@gmail.com';

    private const MOST_USED_WORDS_COUNT = 5;

    /**
     * Render main page
     *
     * @return Response
     */
    public function getPostsAction(): Response
    {
        return new Response($this->render('blog.html.twig', [
            'posts' => $this->getPosts(),
            'mostUsedWords' => $this->getMostUsedWords(),
            'addPostUrl' => $this->generateUrl('add-article')
        ]));
    }

    /**
     * Add post and return it with used words block
     *
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

        return $this->getJsonResponse([
            'post' => $this->render('post.html.twig', ['post' => $post]),
            'usedWords' => $this->render('mostUsedWordsBlock.html.twig', ['mostUsedWords' => $this->getMostUsedWords()])
        ]);
    }

    /**
     * Create post with uploaded file and other data.
     * It is not good that we create file for a not saved post
     *
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

        //TODO: add CSRF?

        return $errors;
    }

    /**
     * @return Post[]
     */
    private function getPosts(): array
    {
        $serializedPosts = file_get_contents($this->getFilePath('posts.txt'));
        $posts = unserialize($serializedPosts);

        if (!$posts) {
            return [];
        }

        usort($posts, function(Post $a, Post $b) {
            return $b->getTime() <=> $a->getTime();
        });

        return $posts;
    }

    /**
     * Count most used words
     *
     * @param Post[] $posts
     * @return array
     */
    private function findMostUsedWords(array $posts): array
    {
        $allContent = array_reduce($posts, function ($carry, Post $post) {
            $carry .= ' ' . $post->getContent();

            return $carry;
        });

        $words = array_count_values(str_word_count(strtolower($allContent), 1));

        $words = array_filter($words, function ($word) {
            return strlen($word) > 4;
        }, ARRAY_FILTER_USE_KEY);

        //first sort words by entry count desc, then by name asc to guarantee an order
        array_multisort(array_values($words), SORT_DESC, array_keys($words), SORT_ASC, $words);

        return array_slice(array_keys($words), 0, self::MOST_USED_WORDS_COUNT);
    }

    /**
     * Find most used words and save result to the file
     *
     * @param Post[] $posts
     * @return bool|int
     */
    private function updateMostUsedWords(array $posts)
    {
        $mostUsedWords = $this->findMostUsedWords($posts);

        return file_put_contents($this->getFilePath('mostUsedWords.txt'), serialize($mostUsedWords));
    }

    /**
     * Save posts to file
     *
     * @param Post[] $posts
     * @return bool|int
     */
    private function putPosts(array $posts)
    {
        return file_put_contents($this->getFilePath('posts.txt'), serialize($posts));
    }

    /**
     * Return most used words from the file
     *
     * @return string[]|mixed
     */
    private function getMostUsedWords()
    {
        return unserialize(file_get_contents($this->getFilePath('mostUsedWords.txt')));
    }
}
