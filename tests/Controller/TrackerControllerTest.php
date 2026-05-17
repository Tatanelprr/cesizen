<?php

namespace App\Tests\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class TrackerControllerTest extends WebTestCase
{
    public function testTrackerRequiresAuthentication(): void
    {
        $client = static::createClient();
        $client->request('GET', '/tracker');
        $this->assertResponseRedirects('/connexion');
    }

    public function testTrackerIndexAccessibleWhenLoggedIn(): void
    {
        $client = static::createClient();
        $container = static::getContainer();

        $user = new User();
        $user->setEmail('test_tracker@cesizen.fr');
        $user->setPseudo('TestUser');
        $hasher = $container->get(UserPasswordHasherInterface::class);
        $user->setPassword($hasher->hashPassword($user, 'Test@1234'));

        $em = $container->get('doctrine')->getManager();
        $em->persist($user);
        $em->flush();

        $client->loginUser($user);
        $client->request('GET', '/tracker');
        $this->assertResponseIsSuccessful();

        $em->remove($user);
        $em->flush();
    }

    public function testReportRequiresAuthentication(): void
    {
        $client = static::createClient();
        $client->request('GET', '/tracker/rapport');
        $this->assertResponseRedirects('/connexion');
    }
}
