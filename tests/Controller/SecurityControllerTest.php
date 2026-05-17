<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SecurityControllerTest extends WebTestCase
{
    public function testLoginPageLoads(): void
    {
        $client = static::createClient();
        $client->request('GET', '/connexion');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('form');
        $this->assertSelectorExists('input[name="email"]');
        $this->assertSelectorExists('input[name="password"]');
    }

    public function testRegisterPageLoads(): void
    {
        $client = static::createClient();
        $client->request('GET', '/inscription');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('form');
    }

    public function testLoginWithWrongCredentials(): void
    {
        $client = static::createClient();
        $client->request('POST', '/connexion', [
            'email'      => 'faux@email.fr',
            'password'   => 'mauvaismdp',
            '_csrf_token' => 'fake',
        ]);
        $this->assertResponseRedirects('/connexion');
    }

    public function testLogoutRedirects(): void
    {
        $client = static::createClient();
        $client->request('GET', '/deconnexion');
        $this->assertResponseRedirects();
    }
}
