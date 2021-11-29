<?php

namespace App\Tests\Post;

use App\Tests\BaseTestCase;
use Symfony\Component\HttpFoundation\Response;

class DeleteTest extends BaseTestCase
{
    public function test_attempt_to_delete_a_post(): void
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

        $client->request('DELETE', "/api/post/$postId");

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }
}
