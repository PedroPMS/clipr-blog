<?php

namespace App\Service\Auth;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AuthService
{
    private EntityManagerInterface $entityManager;
    private UserPasswordEncoderInterface $encoder;

    public function __construct(EntityManagerInterface $entityManager, UserPasswordEncoderInterface $encoder)
    {
        $this->entityManager = $entityManager;
        $this->encoder = $encoder;
    }

    public function register(User $user, FormInterface $form): void
    {
        $user->setPassword(
            $this->encoder->encodePassword(
                $user,
                $form->get('password')->getData()
            )
        );

        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }
}
