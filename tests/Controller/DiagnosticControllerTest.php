<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DiagnosticControllerTest extends WebTestCase
{
    public function testIndexLoads(): void
    {
        $client = static::createClient();
        $client->request('GET', '/diagnostic');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('form');
    }

    public function testIndexAccessibleWithoutLogin(): void
    {
        $client = static::createClient();
        $client->request('GET', '/diagnostic');
        $this->assertResponseIsSuccessful();
    }

    public function testResultWithNoEvents(): void
	{
		$client = static::createClient();
		$client->request('POST', '/diagnostic/resultat', []);
		$this->assertResponseIsSuccessful();
	}

	public function testResultWithEvents(): void
	{
		$client = static::createClient();
		$client->request('GET', '/diagnostic');
		$this->assertResponseIsSuccessful();

		$client->request('POST', '/diagnostic/resultat', [
			'events' => ['1', '2'],
		]);
		$this->assertResponseIsSuccessful();
	}
}
