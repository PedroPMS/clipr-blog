<?php

namespace App\Tests\Post;

use App\Tests\BaseTestCase;
use Symfony\Component\HttpFoundation\Response;

class CreateTest extends BaseTestCase
{
    public function test_attempt_to_create_a_new_post(): void
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

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    public function test_attempt_to_create_a_new_post_as_a_not_writer(): void
    {
        $client = $this->createAuthenticatedClient();
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

        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
    }

    public function test_attempt_to_create_a_new_post_with_invalid_data(): void
    {
        $client = $this->createAuthenticatedClient('writer@gmail.com');
        $client->request(
            'POST',
            '/api/post',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([])
        );

        $this->assertResponseStatusCodeSame(Response::HTTP_UNPROCESSABLE_ENTITY);
    }
}
