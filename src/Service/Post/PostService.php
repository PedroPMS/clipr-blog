<?php

namespace App\Service\Post;

use App\Entity\Post;
use Symfony\Component\Security\Core\User\UserInterface;

class PostService
{
    public function checkUserOfPost(UserInterface $user, Post $post): bool
    {
        return $user->getId() == $post->getUser()->getId();
    }
}
