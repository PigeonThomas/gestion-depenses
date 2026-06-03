<?php

namespace App\Tests\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class RegistrationControllerTest extends WebTestCase
{
    // Test de l'accès à la page d'inscription (doit être accessible pour un utilisateur anonyme)
    public function testRegistrationPageIsAccessible(): void
    {
        $client = static::createClient();
        $client->request('GET', '/register');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('form');
    }

    // Test de la présence des champs attendus dans le formulaire d'inscription
    public function testRegistrationPageContainsExpectedFields(): void
    {
        $client = static::createClient();
        $client->request('GET', '/register');

        $this->assertSelectorExists('input[name*="email"]');
        $this->assertSelectorExists('input[name*="plainPassword"]');
    }

    // Test de la soumission du formulaire d'inscription avec des données valides (doit créer un utilisateur et rediriger)
    public function testAlreadyAuthenticatedUserIsRedirectedFromRegister(): void
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
        $client->request('GET', '/register');

        // Un utilisateur déjà connecté doit être redirigé
        $this->assertResponseRedirects();
    }
}
