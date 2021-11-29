<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Service\Auth\AuthService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class AuthController extends AbstractController
{
    private AuthService $registerService;

    public function __construct(AuthService $registerService)
    {
        $this->registerService = $registerService;
    }

    public function register(Request $request): JsonResponse
    {
        $user = new User();

        $body = json_decode($request->getContent(), true);

        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->submit($body);

        if (!$form->isValid()) {
            return $this->handleValidationErrors($form);
        }

        $this->registerService->register($user, $form);
        return $this->json(['message' => 'User successfully created'], 201);
    }

    private function handleValidationErrors(FormInterface $form): JsonResponse
    {
        $errors = [];
        foreach ($form->getErrors(true, true) as $error) {
            $errors[$error->getOrigin()->getName()] = $error->getMessage();
        }

        return $this->json([
            'message' => 'The given data was invalid.',
            'errors' => $errors
        ]);
    }
}
