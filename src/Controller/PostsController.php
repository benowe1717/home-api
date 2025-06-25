<?php

/**
 * Symfony Controller for /api/v1/posts Route
 *
 * PHP version 8.4
 *
 * @category  Controller
 * @package   Home-API
 * @author    Benjamin Owen <benjamin@projecttiy.com>
 * @copyright 2025 Benjamin Owen
 * @license   https://mit-license.org/ MIT
 * @version   CVS: $Id:$
 * @link      https://github.com/benowe1717/home-api
 **/

namespace App\Controller;

use App\Entity\Post;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Symfony Controller for /api/v1/posts Route
 *
 * PHP version 8.4
 *
 * @category  Controller
 * @package   Home-API
 * @author    Benjamin Owen <benjamin@projecttiy.com>
 * @copyright 2025 Benjamin Owen
 * @license   https://mit-license.org/ MIT
 * @version   Release: 0.0.1
 * @link      https://github.com/benowe1717/home-api
 **/
final class PostsController extends AbstractController
{
    private EntityManagerInterface $entityManagerInterface;

    /**
     * PostsController constructor
     *
     * @param EntityManagerInterface $entityManagerInterface The Entity Manager
     **/
    public function __construct(EntityManagerInterface $entityManagerInterface)
    {
        $this->entityManagerInterface = $entityManagerInterface;
    }

    /**
     * Return all Posts from the database
     *
     * @return array
     **/
    private function getPostsFromDatabase(): array
    {
        $repository = $this->entityManagerInterface->getRepository(Post::class);
        return $repository->findAll();
    }

    /**
     * Return a single Post from the database using the given ID
     *
     * @param int $id The Post ID
     *
     * @return ?Post
     **/
    private function getPostFromDatabase(int $id): ?Post
    {
        $repository = $this->entityManagerInterface->getRepository(Post::class);
        return $repository->find($id);
    }

    /**
     * Return a Post as an array to encode into JSON
     *
     * @param Post $post The Post Entity
     *
     * @return array
     **/
    private function representPost(Post $post): array
    {
        $postAsArray = array(
            'id' => $post->getId(),
            'created_at' => $post->getCreated(),
            'updated_at' => $post->getUpdated(),
            'author' => $post->getAuthor()->getUserIdentifier(),
            'content' => $post->getContent()
        );
        return $postAsArray;
    }

    /**
     * /api/v1/posts Route to list all Posts
     *
     * @return JsonResponse
     **/
    #[Route('/api/v1/posts', name: 'app_posts_v1', methods: ['GET'])]
    public function getPosts(): JsonResponse
    {
        $posts = $this->getPostsFromDatabase();
        $count = count($posts);

        $allPosts = array();
        foreach ($posts as $entity) {
            array_push($allPosts, $this->representPost($entity));
        }

        return $this->json(['total' => $count, 'data' => $allPosts]);
    }

    /**
     * /api/v1/posts/{id} Route to list, update, or delete a specific Post
     *
     * @param int                $id        The Post ID
     * @param Request            $request   The HTTP Request
     * @param ValidatorInterface $validator The Validator for Entities
     *
     * @return JsonResponse
     **/
    #[Route(
        '/api/v1/posts/{id}',
        name: 'app_post_v1',
        methods: ['GET', 'DELETE', 'PUT']
    )]
    public function getPost(
        int $id,
        Request $request,
        ValidatorInterface $validator
    ): JsonResponse {
        $post = $this->getPostFromDatabase($id);

        if (null === $post) {
            $result = array(
                'result' => 'failed',
                'reason' => 'Post does not exist!'
            );
            return $this->json($result, JsonResponse::HTTP_NOT_FOUND);
        }

        if ($request->getMethod() === 'PUT') {
            $user = $this->getUser();
            if ($user !== $post->getAuthor()) {
                $result = array(
                    'result' => 'failed',
                    'reason' => "You cannot update another user's posts!"
                );
                return $this->json($result, JsonResponse::HTTP_FORBIDDEN);
            }

            $data = $request->toArray();
            if (!array_key_exists('content', $data)) {
                $result = array(
                    'result' => 'failed',
                    'reason' => 'Posts require a `content` key!'
                );
                return $this->json($result, JsonResponse::HTTP_BAD_REQUEST);
            }

            $now = date('Y-m-d H:i:s', time());
            $post->setContent($data['content']);
            $post->setUpdated(new DateTime($now));

            $errors = $validator->validate($post);
            if ($errors->count() > 0) {
                $result['result'] = 'failed';
                $result['reasons'] = array();

                foreach ($errors as $error) {
                    array_push(
                        $result['reasons'],
                        array('reason' => $error->getMessage())
                    );
                }
                return $this->json($result, JsonResponse::HTTP_BAD_REQUEST);
            }

            $this->entityManagerInterface->persist($post);
            $this->entityManagerInterface->flush();
        } elseif ($request->getMethod() === 'DELETE') {
            $user = $this->getUser();
            if ($user !== $post->getAuthor()) {
                $result = array(
                    'result' => 'failed',
                    'reason' => "You cannot remove another user's posts!"
                );
                return $this->json($result, JsonResponse::HTTP_FORBIDDEN);
            }

            $this->entityManagerInterface->remove($post);
            $this->entityManagerInterface->flush();

            return $this->json([], JsonResponse::HTTP_NO_CONTENT);
        }

        return $this->json($this->representPost($post));
    }

    /**
     * /api/v1/posts/create Route to Create a Post
     *
     * @param Request            $request   The HTTP Request
     * @param ValidatorInterface $validator The Validator for Entities
     *
     * @return JsonResponse
     **/
    #[Route('/api/v1/posts/create', name: 'app_create_post_v1', methods: ['POST'])]
    public function createPost(
        Request $request,
        ValidatorInterface $validator
    ): JsonResponse {
        $result = array('result' => 'success');

        $data = $request->toArray();
        $user = $this->getUser();

        if (!array_key_exists('content', $data)) {
            $result['result'] = 'failed';
            $result['reason'] = 'Posts require a `content` key!';
            return $this->json($result, JsonResponse::HTTP_BAD_REQUEST);
        }

        $now = date('Y-m-d H:i:s', time());
        $post = new Post();
        $post->setCreated(new DateTime($now));
        $post->setAuthor($user);
        $post->setContent($data['content']);

        $errors = $validator->validate($post);
        if ($errors->count() > 0) {
            $result['result'] = 'failed';
            $result['reasons'] = array();

            foreach ($errors as $error) {
                array_push(
                    $result['reasons'],
                    array('reason' => $error->getMessage())
                );
            }
            return $this->json($result, JsonResponse::HTTP_BAD_REQUEST);
        }

        $this->entityManagerInterface->persist($post);
        $this->entityManagerInterface->flush();

        $result['data'] = $this->representPost($post);

        return $this->json($result);
    }
}
