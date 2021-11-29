<?php

namespace App\DataFixtures;

use App\Entity\Role;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class RoleFixtures extends Fixture
{
    public const ROLE_ADMIN = 'admin-user';
    public const ROLE_WRITER = 'writer-user';

    public function load(ObjectManager $manager): void
    {
        $roles = [['admin', 'ROLE_ADMIN'], ['writer', 'ROLE_WRITER']];

        foreach ($roles as $role) {
            $newRole = new Role();
            $newRole->setName($role[0]);
            $newRole->setRoleName($role[1]);
            $newRole->setStatus(true);
            $manager->persist($newRole);

            if ($role[0] == 'admin') {
                $this->addReference(self::ROLE_ADMIN, $newRole);
            } else {
                $this->addReference(self::ROLE_WRITER, $newRole);
            }
        }

        $manager->flush();
    }
}
