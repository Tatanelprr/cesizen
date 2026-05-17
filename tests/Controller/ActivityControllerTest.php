<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ActivityControllerTest extends WebTestCase
{
    public function testIndexLoads(): void
    {
        $client = static::createClient();
        $client->request('GET', '/activites');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('h1');
    }

    public function testIndexAccessibleWithoutLogin(): void
    {
        $client = static::createClient();
        $client->request('GET', '/activites');
        $this->assertResponseIsSuccessful();
    }

    public function testFilterByType(): void
    {
        $client = static::createClient();
        $client->request('GET', '/activites?type=meditation');
        $this->assertResponseIsSuccessful();
    }

    public function testShowNonExistentActivityReturns404(): void
    {
        $client = static::createClient();
        $client->request('GET', '/activites/99999');
        $this->assertResponseStatusCodeSame(404);
    }
}
