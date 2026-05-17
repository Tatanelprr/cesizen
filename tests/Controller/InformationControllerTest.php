<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class InformationControllerTest extends WebTestCase
{
    public function testIndexLoads(): void
    {
        $client = static::createClient();
        $client->request('GET', '/informations');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('h1');
    }

    public function testIndexAccessibleWithoutLogin(): void
    {
        $client = static::createClient();
        $client->request('GET', '/informations');
        $this->assertResponseIsSuccessful();
    }

    public function testShowNonExistentArticleReturns404(): void
    {
        $client = static::createClient();
        $client->request('GET', '/informations/article-inexistant');
        $this->assertResponseStatusCodeSame(404);
    }
}
