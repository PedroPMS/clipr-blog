<?php

namespace App\Controller;

use App\Entity\Post;
use App\Form\PostType;
use App\Repository\PostRepository;
use App\Service\Post\PostService;
use App\Utils\ValidationErrorsHandler;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\View\View;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Controller\Annotations as Rest;
use OpenApi\Annotations as OA;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use Symfony\Component\HttpFoundation\Response;

class PostController extends AbstractController
{
    /**
     * @Rest\Get("/api/post", name="post_list")
     * @Rest\View(serializerGroups={"post", "timestamps", "comment"})
     *
     * List all posts.
     *
     * @OA\Response(
     *     response=200,
     *     description="Returns all posts",
     *     @OA\JsonContent(
     *        type="array",
     *        @OA\Items(ref=@Model(type= App\Entity\Post::class, groups={"post", "timestamps", "comment"}))
     *     )
     * )
     *
     * @OA\Tag(name="Post")
     */
    public function index(PostRepository $postRepository): View
    {
        $posts = $postRepository->findAll();

        return View::create($posts);
    }

    /**
     * @Rest\Get("/api/post/user", name="users_post_list")
     * @Rest\View(serializerGroups={"post", "timestamps", "comment"})
     *
     * List all posts of the logged user.
     *
     * @OA\Response(
     *     response=200,
     *     description="Returns all posts of a user",
     *     @OA\JsonContent(
     *        type="array",
     *        @OA\Items(ref=@Model(type= App\Entity\Post::class, groups={"post", "timestamps", "comment"}))
     *     )
     * )
     *
     * @OA\Tag(name="Post")
     * @Security(name="Bearer")
     */
    public function getUsersPosts(PostRepository $postRepository): View
    {
        $posts = $postRepository->findBy(['user' => $this->getUser()->getId()]);

        return View::create($posts);
    }

    /**
     * @Rest\Post("/api/post", name="post_create")
     * @Rest\View(serializerGroups={"post", "timestamps", "comment"})
     *
     * Create a post.
     *
     * @OA\RequestBody(
     *     @OA\MediaType(
     *     mediaType="application/json",
     *          @OA\Schema(ref=@Model(type= App\Form\PostType::class))
     *     )
     * )
     *
     * @OA\Response(
     *     response=201,
     *     description="Create a post",
     *     @OA\JsonContent(
     *        type="array",
     *        @OA\Items(ref=@Model(type= App\Entity\Post::class, groups={"post", "timestamps", "comment"}))
     *     )
     * )
     *
     * @OA\Response(
     *     response=422,
     *     description="Invalid data"
     * )
     *
     * @OA\Tag(name="Post")
     * @Security(name="Bearer")
     */
    public function create(Request $request, EntityManagerInterface $entityManager): View
    {
        $this->denyAccessUnlessGranted('ROLE_WRITER');
        $post = new Post();
        $post->setUser($this->getUser());

        $body = json_decode($request->getContent(), true);

        $form = $this->createForm(PostType::class, $post);
        $form->submit($body);

        if (!$form->isValid()) {
            return (new ValidationErrorsHandler())($form);
        }

        $entityManager->persist($post);
        $entityManager->flush();
        return View::create($post, Response::HTTP_CREATED);
    }

    /**
     * @Rest\Put("/api/post/{id}", name="post_update")
     * @Rest\View(serializerGroups={"post", "timestamps", "comment"})
     *
     * Update a post.
     *
     * @OA\RequestBody(
     *     @OA\MediaType(
     *     mediaType="application/json",
     *          @OA\Schema(ref=@Model(type= App\Form\PostType::class))
     *     )
     * )
     *
     * @OA\Response(
     *     response=201,
     *     description="Update a post",
     *     @OA\JsonContent(
     *        type="array",
     *        @OA\Items(ref=@Model(type= App\Entity\Post::class, groups={"post", "timestamps", "comment"}))
     *     )
     * )
     *
     * @OA\Response(
     *     response=422,
     *     description="Invalid data"
     * )
     *
     * @OA\Response(
     *     response=404,
     *     description="Post not found"
     * )
     *
     * @OA\Tag(name="Post")
     * @Security(name="Bearer")
     */
    public function edit(Request $request, PostService $postService, Post $post, EntityManagerInterface $entityManager): View
    {
        $this->denyAccessUnlessGranted('ROLE_WRITER');
        $isValidUser = $postService->checkUserOfPost($this->getUser(), $post);
        if (!$isValidUser) {
            return View::create(['message' => 'You cannot update this post'], Response::HTTP_UNAUTHORIZED);
        }

        $body = json_decode($request->getContent(), true);

        $form = $this->createForm(PostType::class, $post);
        $form->submit($body, false);

        if (!$form->isValid()) {
            return (new ValidationErrorsHandler())($form);
        }

        $entityManager->flush();
        return View::create($post);
    }

    /**
     * @Rest\Delete("/api/post/{id}", name="post_delete")
     *
     * @OA\Response(
     *     response=200,
     *     description="Success"
     * )
     *
     * @OA\Response(
     *     response=404,
     *     description="Post not found"
     * )
     *
     * @OA\Tag(name="Post")
     * @Security(name="Bearer")
     */
    public function delete(PostService $postService, Post $post, EntityManagerInterface $entityManager): View
    {
        $this->denyAccessUnlessGranted('ROLE_WRITER');
        $isValidUser = $postService->checkUserOfPost($this->getUser(), $post);
        if (!$isValidUser) {
            return View::create(['message' => 'You cannot update this post'], Response::HTTP_UNAUTHORIZED);
        }

        $entityManager->remove($post);
        $entityManager->flush();

        return View::create(['message' => 'OK']);
    }
}
