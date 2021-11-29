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
use Symfony\Component\HttpFoundation\Response;

class PostController extends AbstractController
{
    /**
     * @Rest\Get("/api/post", name="post_list")
     * @Rest\View(serializerGroups={"post", "timestamps", "comment"})
     */
    public function index(PostRepository $postRepository): View
    {
        $posts = $postRepository->findAll();

        return View::create($posts);
    }

    /**
     * @Rest\Get("/api/post/user", name="users_post_list")
     * @Rest\View(serializerGroups={"post", "timestamps", "comment"})
     */
    public function getUsersPosts(PostRepository $postRepository): View
    {
        $posts = $postRepository->findBy(['user' => $this->getUser()->getId()]);

        return View::create($posts);
    }

    /**
     * @Rest\Post("/api/post", name="post_create")
     * @Rest\View(serializerGroups={"post", "timestamps", "comment"})
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
            return View::create((new ValidationErrorsHandler())($form), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $entityManager->persist($post);
        $entityManager->flush();
        return View::create($post, Response::HTTP_CREATED);
    }

    /**
     * @Rest\Put("/api/post/{id}", name="post_update")
     * @Rest\View(serializerGroups={"post", "timestamps", "comment"})
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
            return View::create((new ValidationErrorsHandler())($form), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $entityManager->flush();
        return View::create($post);
    }

    /**
     * @Rest\Delete("/api/post/{id}", name="post_delete")
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
