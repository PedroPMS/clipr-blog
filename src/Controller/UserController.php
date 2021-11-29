<?php

namespace App\Controller;

use App\Repository\UserRepository;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use OpenApi\Annotations as OA;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use FOS\RestBundle\View\View;

class UserController extends AbstractFOSRestController
{
    /**
     * @Rest\Get("/api/users", name="users_list")
     * @Rest\View(serializerGroups={"user"})
     *
     * List all users.
     *
     * List all users and roles for admins.
     *
     * @OA\Response(
     *     response=200,
     *     description="Returns the rewards of an user",
     *     @OA\JsonContent(
     *        type="array",
     *        @OA\Items(ref=@Model(type= App\Entity\User::class, groups={"user"}))
     *     )
     * )
     * @OA\Tag(name="User")
     * @Security(name="Bearer")
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
     *
     * Detail specified user.
     *
     * Return the data of logged user.
     *
     * @OA\Response(
     *     response=200,
     *     description="Returns the rewards of an user",
     *     @OA\JsonContent(ref=@Model(type= App\Entity\User::class, groups={"user"}))
     * )
     * @OA\Tag(name="User")
     * @Security(name="Bearer")
     */
    public function show(UserRepository $userRepository): View
    {
        $user = $userRepository->findOneBy(['email' => $this->getUser()->getUserIdentifier()]);

        return  View::create($user);
    }
}
