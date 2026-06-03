<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SecurityControllerTest extends WebTestCase
{
    // Test de l'accès à la page de login (doit être accessible pour un utilisateur anonyme)
    public function testLoginPageIsAccessible(): void
    {
        $client = static::createClient();
        $client->request('GET', '/login');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('form');
    }

    // Test de la présence des champs attendus dans le formulaire de login
    public function testLoginPageContainsEmailAndPasswordFields(): void
    {
        $client = static::createClient();
        $client->request('GET', '/login');

        $this->assertSelectorExists('input[name="_username"]');
        $this->assertSelectorExists('input[name="_password"]');
    }

    // Test de la soumission du formulaire de login avec des données valides (doit rediriger vers le dashboard)
    public function testLoginWithInvalidCredentialsShowsError(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/login');

        $form = $crawler->selectButton('Connexion')->form([
            '_username' => 'invalid@example.com',
            '_password' => 'wrongpassword',
        ]);
        $client->submit($form);

        // After failed login, redirected back to login page
        $this->assertResponseRedirects('/login');
        $client->followRedirect();
        $this->assertSelectorExists('.alert');
    }

    // Test de la soumission du formulaire de login avec des données valides (doit rediriger vers le dashboard)
    public function testLogoutRedirects(): void
    {
        $client = static::createClient();
        $client->request('GET', '/logout');

        $this->assertResponseRedirects();
    }
}
