<?php

namespace App\Tests\ApiTest;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;

class CategoryTest extends ApiTestCase
{
    public function testSomething(): void
    {
        $response = static::createClient()->request('GET', '/');

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains(['@id' => '/']);
    }
}
