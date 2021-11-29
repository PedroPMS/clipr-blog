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

class AuthController extends AbstractController
{
    private AuthService $registerService;

    public function __construct(AuthService $registerService)
    {
        $this->registerService = $registerService;
    }

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
