<?php

namespace App\Tests\User;

use App\Tests\BaseTestCase;
use Symfony\Component\HttpFoundation\Response;

class UserTest extends BaseTestCase
{
    public function test_attempt_to_get_all_users_as_admin(): void
    {
        $client = $this->createAuthenticatedClient();
        $client->request('GET', '/api/users');

        $this->assertEquals(Response::HTTP_OK, $client->getResponse()->getStatusCode());
    }

    public function test_attempt_to_get_all_users_as_writer(): void
    {
        $client = $this->createAuthenticatedClient('writer@gmail.com');
        $client->request('GET', '/api/users');

        $this->assertEquals(Response::HTTP_FORBIDDEN, $client->getResponse()->getStatusCode());
    }

    public function test_attempt_to_get_profile_as_admin(): void
    {
        $client = $this->createAuthenticatedClient();
        $client->request('GET', '/api/profile');

        $this->assertEquals(Response::HTTP_OK, $client->getResponse()->getStatusCode());
    }

    public function test_attempt_to_get_profile_as_write(): void
    {
        $client = $this->createAuthenticatedClient('writer@gmail.com');
        $client->request('GET', '/api/profile');

        $this->assertEquals(Response::HTTP_OK, $client->getResponse()->getStatusCode());
    }
}
