<?php

namespace App\Tests\Comment;

use App\Tests\BaseTestCase;
use Symfony\Component\HttpFoundation\Response;

class ListTest extends BaseTestCase
{
    public function test_attempt_to_get_all_comments_of_post_as_logged_user(): void
    {
        $client = $this->createAuthenticatedClient();
        $client->request('GET', '/api/post/2/comment');

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    public function test_attempt_to_get_all_comments_of_post_as_anonymous(): void
    {
        $client = static::createClient();
        $client->request('GET', '/api/post/2/comment');

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }
}
