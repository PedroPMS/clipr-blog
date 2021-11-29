<?php

namespace App\Controller;

use App\Repository\UserRepository;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\View;

class UserController extends AbstractFOSRestController
{
    /**
     * @Rest\Get("/api/users", name="users_list")
     * @Rest\View(serializerGroups={"user"})
     */
    public function index(UserRepository $userRepository): View
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $users = $userRepository->findAll();

        return  View::create($users);
    }

    /**
     * @Rest\Get("/api/profile", name="user_profile")
     * @Rest\View(serializerGroups={"user"})
     */
    public function show(UserRepository $userRepository): View
    {
        $user = $userRepository->findOneBy(['email' => $this->getUser()->getUserIdentifier()]);

        return  View::create($user);
    }
}