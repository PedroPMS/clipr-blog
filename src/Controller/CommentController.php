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
use OpenApi\Annotations as OA;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use Symfony\Component\HttpFoundation\Response;

class CommentController extends AbstractController
{
    /**
     * @Rest\Get("/api/post/{id}/comment", name="post_comment_list")
     * @Rest\View(serializerGroups={"comment", "timestamps"})
     *
     * List all comments of a post.
     *
     * @OA\Response(
     *     response=200,
     *     description="Returns all comments of a post",
     *     @OA\JsonContent(
     *        type="array",
     *        @OA\Items(ref=@Model(type= App\Entity\Comment::class, groups={"comment", "timestamps"}))
     *     )
     * )
     *
     * @OA\Response(
     *     response=404,
     *     description="Post not found"
     * )
     *
     * @OA\Tag(name="Comment")
     */
    public function index(Post $post, CommentRepository $commentRepository): View
    {
        $comments = $commentRepository->findBy(['post' => $post->getId()]);

        return View::create($comments);
    }

    /**
     * @Rest\Post("/api/post/{id}/comment", name="post_comment_create")
     * @Rest\View(serializerGroups={"comment", "timestamps"})
     *
     * Create a comment in a post.
     *
     * @OA\RequestBody(
     *     @OA\MediaType(
     *     mediaType="application/json",
     *          @OA\Schema(ref=@Model(type= App\Form\CommentType::class))
     *     )
     * )
     *
     * @OA\Response(
     *     response=201,
     *     description="Create a comment in a post",
     *     @OA\JsonContent(
     *        type="array",
     *        @OA\Items(ref=@Model(type= App\Entity\Comment::class, groups={"comment", "timestamps"}))
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
     * @OA\Tag(name="Comment")
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
            return (new ValidationErrorsHandler())($form);
        }

        $entityManager->persist($comment);
        $entityManager->flush();
        return View::create($comment, Response::HTTP_CREATED);
    }
}
