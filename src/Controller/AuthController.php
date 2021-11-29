<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Service\Auth\AuthService;
use App\Utils\ValidationErrorsHandler;
use FOS\RestBundle\View\View;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use OpenApi\Annotations as OA;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;

class AuthController extends AbstractController
{
    private AuthService $registerService;

    public function __construct(AuthService $registerService)
    {
        $this->registerService = $registerService;
    }

    /**
     * Create a post.
     *
     * @OA\RequestBody(
     *     @OA\MediaType(
     *     mediaType="application/json",
     *          @OA\Schema(ref=@Model(type= App\Form\RegistrationFormType::class))
     *     )
     * )
     *
     *
     * @OA\Response(
     *     response=422,
     *     description="Invalid data"
     * )
     *
     * @OA\Response(
     *     response=201,
     *     description="User created"
     * )
     *
     * @OA\Tag(name="Auth")
     */
    public function register(Request $request): View
    {
        $user = new User();

        $body = json_decode($request->getContent(), true);

        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->submit($body);

        if (!$form->isValid()) {
            return View::create((new ValidationErrorsHandler())($form), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $this->registerService->register($user, $form);
        return View::create(['message' => 'User successfully created'], Response::HTTP_CREATED);
    }
}
