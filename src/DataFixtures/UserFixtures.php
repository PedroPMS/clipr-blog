<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixtures extends Fixture
{
    public const USER_WRITER = 'writer-user';

    private UserPasswordEncoderInterface $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
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
        $this->addReference(self::USER_WRITER, $newUser);

        $manager->flush();
    }
}
