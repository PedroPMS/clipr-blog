<?php

namespace App\DataFixtures;

use App\Entity\Post;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;


class PostFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $userWriter = $this->getReference(UserFixtures::USER_WRITER);

        $posts = [
            ['title 1', 'lorem ipsum'],
            ['title 2', 'lorem ipsum is away']
        ];

        foreach ($posts as $post) {
            $newPost = new Post();
            $newPost->setTitle($post[0]);
            $newPost->setContent($post[1]);
            $newPost->setUser($userWriter);

            $manager->persist($newPost);
        }

        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            UserFixtures::class,
        ];
    }
}
