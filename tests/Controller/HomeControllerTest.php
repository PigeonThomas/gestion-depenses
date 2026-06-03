<?php

namespace App\Tests\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class HomeControllerTest extends WebTestCase
{
    /**
     * Helper : crée un client authentifié avec un utilisateur persisté en BDD de test.
     */
    private function createAuthenticatedClient(): \Symfony\Bundle\FrameworkBundle\KernelBrowser
    {
        $client = static::createClient();
        $container = static::getContainer();

        /** @var EntityManagerInterface $em */
        $em = $container->get('doctrine')->getManager();
        $hasher = $container->get(UserPasswordHasherInterface::class);

        // Nettoie l'utilisateur de test s'il existe déjà
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

    // --- Page d'accueil ---

    // Test de l'accès à la page d'accueil (doit être accessible même pour un utilisateur anonyme)
    public function testHomePageIsAccessible(): void
    {
        $client = static::createClient();
        $client->request('GET', '/');

        $this->assertResponseIsSuccessful();
    }

    // --- Dashboard : accès anonyme ---

    // Test de l'accès à la page de dashboard pour un utilisateur anonyme (doit rediriger vers la page de login)
    public function testDashboardRedirectsAnonymousUser(): void
    {
        $client = static::createClient();
        $client->request('GET', '/dashboard');

        $this->assertResponseRedirects('/login');
    }

    // --- Dashboard : accès authentifié ---

    // Test de l'accès à la page de dashboard pour un utilisateur authentifié (doit être accessible)
    public function testDashboardIsAccessibleForAuthenticatedUser(): void
    {
        $client = $this->createAuthenticatedClient();
        $client->request('GET', '/dashboard');

        $this->assertResponseIsSuccessful();
    }

    // Test de la présence des sections et liens de filtrage par période sur le dashboard
    public function testDashboardContainsExpectedSections(): void
    {
        $client = $this->createAuthenticatedClient();
        $client->request('GET', '/dashboard');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Dashboard');
        $this->assertSelectorExists('a[href*="period=current"]');
        $this->assertSelectorExists('a[href*="period=last"]');
    }

    // --- Dashboard : filtre par période ---

    // Test de l'accès au dashboard avec le paramètre de période "current" (doit être accessible)
    public function testDashboardWithCurrentPeriodParameter(): void
    {
        $client = $this->createAuthenticatedClient();
        $client->request('GET', '/dashboard?period=current');

        $this->assertResponseIsSuccessful();
    }

    // Test de l'accès au dashboard avec le paramètre de période "last" (doit être accessible)
    public function testDashboardWithLastPeriodParameter(): void
    {
        $client = $this->createAuthenticatedClient();
        $client->request('GET', '/dashboard?period=last');

        $this->assertResponseIsSuccessful();
    }

    // Test de l'accès au dashboard avec un paramètre de période invalide (doit rester accessible sans erreur)
    public function testDashboardWithInvalidPeriodFallsBackToCurrent(): void
    {
        $client = $this->createAuthenticatedClient();
        $client->request('GET', '/dashboard?period=invalid');

        // Doit rester accessible sans erreur (fallback sur 'current')
        $this->assertResponseIsSuccessful();
    }
}
