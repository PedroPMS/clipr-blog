<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Post;
use App\Form\CommentType;
use App\Repository\CommentRepository;
use App\Utils\ValidationErrorsHandler;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\View\View;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpFoundation\Response;

class CommentController extends AbstractController
{
    /**
     * @Rest\Get("/api/post/{id}/comment", name="post_comment_list")
     * @Rest\View(serializerGroups={"comment", "timestamps"})
     */
    public function index(Post $post, CommentRepository $commentRepository): View
    {
        $comments = $commentRepository->findBy(['post' => $post->getId()]);

        return View::create($comments);
    }

    /**
     * @Rest\Post("/api/post/{id}/comment", name="post_comment_create")
     * @Rest\View(serializerGroups={"comment", "timestamps"})
     */
    public function create(Request $request, Post $post, EntityManagerInterface $entityManager): View
    {
        $comment = new Comment();
        $comment->setPost($post);

        if($this->getUser()) {
            $comment->setUser($this->getUser());
        }

        $body = json_decode($request->getContent(), true);

        $form = $this->createForm(CommentType::class, $comment);
        $form->submit($body);

        if (!$form->isValid()) {
            return View::create((new ValidationErrorsHandler())($form), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $entityManager->persist($comment);
        $entityManager->flush();
        return View::create($comment, Response::HTTP_CREATED);
    }
}
