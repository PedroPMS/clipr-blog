<?php

namespace App\DataFixtures;

use App\Entity\Role;
use App\Entity\User;
use App\Repository\RoleRepository;
use App\Repository\UserRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixtures extends Fixture
{
    private UserPasswordEncoderInterface $encoder;
    private RoleRepository $roleRepository;
    private EntityManagerInterface $em;

    public function __construct(UserPasswordEncoderInterface $encoder, RoleRepository $roleRepository, EntityManagerInterface $em)
    {
        $this->encoder = $encoder;
        $this->roleRepository = $roleRepository;
        $this->em = $em;
    }

    public function load(ObjectManager $manager): void
    {
        $roleAdmin = $this->getReference(RoleFixtures::ROLE_ADMIN);
        $roleWriter = $this->getReference(RoleFixtures::ROLE_WRITER);
        $users = [['admin@gmail.com', $roleAdmin], ['writer@gmail.com', $roleWriter]];

        foreach ($users as $user) {
            $newUser = new User();
            $newUser->setEmail($user[0]);
            $newUser->setPassword(
                $this->encoder->encodePassword(
                    $newUser,
                    'secret123'
                )
            );
            $newUser->addRole($user[1]);

            $manager->persist($newUser);
        }

        $manager->flush();
    }
}
