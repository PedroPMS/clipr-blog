<?php

namespace App\Tests\Post;

use App\Tests\BaseTestCase;
use Symfony\Component\HttpFoundation\Response;

class UpdateTest extends BaseTestCase
{
    public function test_attempt_to_update_a_post(): void
    {
        $client = $this->createAuthenticatedClient('writer@gmail.com');
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
        $postId = json_decode($client->getResponse()->getContent())->id;

        $client->request(
            'PUT',
            "/api/post/$postId",
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'title' => 'title 1',
                'content' => 'lorem ipsum is a simple',
            ])
        );

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }
}
