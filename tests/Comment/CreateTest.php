<?php

namespace App\Tests\Comment;

use App\Tests\BaseTestCase;
use Symfony\Component\HttpFoundation\Response;

class CreateTest extends BaseTestCase
{
    public function test_attempt_to_create_a_new_comment_as_logged_user(): void
    {
        $client = $this->createAuthenticatedClient('writer@gmail.com');
        $postId = $this->createPost($client);
        $client->request(
            'POST',
            "/api/post/$postId/comment",
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'content' => 'lorem ipsum',
            ])
        );

        $this->assertResponseStatusCodeSame(Response::HTTP_CREATED);
    }

    public function test_attempt_to_create_a_new_comment_as_a_not_logged_user(): void
    {
        $client = static::createClient();

        $client->request(
            'POST',
            "/api/post/2/comment",
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'content' => 'lorem ipsum',
            ])
        );

        $this->assertResponseStatusCodeSame(Response::HTTP_CREATED);
    }

    public function test_attempt_to_create_a_new_comment_with_not_valid_post(): void
    {
        $client = static::createClient();

        $client->request(
            'POST',
            "/api/post/999/comment",
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'content' => 'lorem ipsum',
            ])
        );

        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }

    private function createPost($client): int
    {
        $client->request(
            'POST',
            '/api/post',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'title' => 'title 1',
                'content' => 'lorem ipsum',
            ])
        );

        $this->assertResponseStatusCodeSame(Response::HTTP_CREATED);
        return json_decode($client->getResponse()->getContent())->id;
    }
}
