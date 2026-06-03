<?php

namespace App\Tests\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class DepenseControllerTest extends WebTestCase
{
    private function createAuthenticatedClient(): \Symfony\Bundle\FrameworkBundle\KernelBrowser
    {
        $client = static::createClient();
        $container = static::getContainer();

        /** @var EntityManagerInterface $em */
        $em = $container->get('doctrine')->getManager();
        $hasher = $container->get(UserPasswordHasherInterface::class);

        $existing = $em->getRepository(User::class)->findOneBy(['email' => 'test@example.com']);
        if ($existing) {
            $em->remove($existing);
            $em->flush();
        }

        $user = new User();
        $user->setEmail('test@example.com');
        $user->setNomUser('Test');
        $user->setRoles(['ROLE_USER']);
        $user->setPassword($hasher->hashPassword($user, 'password'));
        $user->setCreatedAt(new \DateTimeImmutable());
        $user->setUpdatedAt(new \DateTimeImmutable());

        $em->persist($user);
        $em->flush();

        $client->loginUser($user);

        return $client;
    }

    // --- Liste des dépenses ---

    // Test de l'accès à la page de liste des dépenses pour un utilisateur anonyme (doit rediriger vers la page de login)
    public function testDepenseIndexRedirectsAnonymousUser(): void
    {
        $client = static::createClient();
        $client->request('GET', '/depense');

        $this->assertResponseRedirects('/login');
    }

    // Test de l'accès à la page de liste des dépenses pour un utilisateur authentifié (doit être accessible)
    public function testDepenseIndexIsAccessibleForAuthenticatedUser(): void
    {
        $client = $this->createAuthenticatedClient();
        $client->request('GET', '/depense');

        $this->assertResponseIsSuccessful();
    }

    // --- Création d'une dépense ---

    // Test de l'accès à la page de création d'une dépense pour un utilisateur anonyme (doit rediriger vers la page de login)
    public function testNewDepensePageRedirectsAnonymousUser(): void
    {
        $client = static::createClient();
        $client->request('GET', '/depense/new');

        $this->assertResponseRedirects('/login');
    }

    // Test de l'accès à la page de création d'une dépense pour un utilisateur authentifié (doit être accessible et contenir un formulaire)
    public function testNewDepensePageIsAccessibleForAuthenticatedUser(): void
    {
        $client = $this->createAuthenticatedClient();
        $client->request('GET', '/depense/new');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('form');
    }
}
