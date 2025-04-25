<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class CategoryControllerTest extends WebTestCase
{
    public function testIndexIsPubliclyAccessible(): void
    {
        $client = static::createClient();
        $client->request('GET', '/category');
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertSelectorExists('body'); // You can specify a more specific selector
    }

    public function testNewCategoryRequiresAdmin(): void
    {
        $client = static::createClient();
        $client->request('GET', '/category/new');
        // We expect a redirect to login or 403
        $this->assertTrue(
            $client->getResponse()->isRedirect() || $client->getResponse()->getStatusCode() === 403,
            'Access to /category/new should be restricted for anonymous users.'
        );
    }

    public function testAdminCanCreateCategory(): void
    {
        $client = static::createClient();
        // Load admin user (you need test data or fixtures)
        $userRepo = static::getContainer()->get('doctrine')->getRepository(\App\Entity\User::class);
        $admin = $userRepo->findOneBy(['email' => 'admin@example.com']);
        $client->loginUser($admin);

        $crawler = $client->request('GET', '/category/new');
        $this->assertResponseIsSuccessful();
        $form = $crawler->selectButton('Save')->form([
            'category[name]' => 'Test Category',
            'category[description]' => 'Test Category Description',
            'category[slug]' => 'test-category'
        ]);
        $client->submit($form);
        $this->assertResponseRedirects('/category');
        $client->followRedirect();
        $this->assertSelectorTextContains('body', 'Test Category');
    }

    public function testAdminCanEditCategory(): void
    {
        $client = static::createClient();
        $userRepo = static::getContainer()->get('doctrine')->getRepository(\App\Entity\User::class);
        $admin = $userRepo->findOneBy(['email' => 'admin@example.com']);
        $client->loginUser($admin);

        // Najpierw utwórz kategorię do edycji
        $crawler = $client->request('GET', '/category/new');
        $form = $crawler->selectButton('Save')->form([
            'category[name]' => 'Edit Me',
            'category[description]' => 'Edit Me Description',
            'category[slug]' => 'edit-me',
        ]);
        $client->submit($form);
        $client->followRedirect();
        $this->assertSelectorTextContains('body', 'Edit Me');

        // Pobierz kategorię z repozytorium
        $categoryRepo = static::getContainer()->get('doctrine')->getRepository(\App\Entity\Category::class);
        $category = $categoryRepo->findOneBy(['slug' => 'edit-me']);
        $this->assertNotNull($category);

        // Przejdź do edycji
        $crawler = $client->request('GET', '/category/' . $category->getSlug() . '/edit');
        $this->assertResponseIsSuccessful();
        $form = $crawler->selectButton('Save Changes')->form([
            'category[name]' => 'Edited Category',
            'category[description]' => 'Edited Description',
            'category[slug]' => 'edited-category',
        ]);
        $client->submit($form);
        $client->followRedirect();
        $this->assertSelectorTextContains('body', 'Edited Category');
    }

    public function testAdminCanDeleteCategory(): void
    {
        $client = static::createClient();
        $userRepo = static::getContainer()->get('doctrine')->getRepository(\App\Entity\User::class);
        $admin = $userRepo->findOneBy(['email' => 'admin@example.com']);
        $client->loginUser($admin);

        // Najpierw utwórz kategorię do usunięcia
        $crawler = $client->request('GET', '/category/new');
        $form = $crawler->selectButton('Save')->form([
            'category[name]' => 'Delete Me',
            'category[description]' => 'Delete Me Description',
            'category[slug]' => 'delete-me',
        ]);
        $client->submit($form);
        $client->followRedirect();
        $this->assertSelectorTextContains('body', 'Delete Me');

        // Pobierz kategorię z repozytorium
        $categoryRepo = static::getContainer()->get('doctrine')->getRepository(\App\Entity\Category::class);
        $category = $categoryRepo->findOneBy(['slug' => 'delete-me']);
        $this->assertNotNull($category);

        // Przejdź do show i wykonaj usunięcie przez formularz
        $crawler = $client->request('GET', '/category/' . $category->getSlug());
        $this->assertResponseIsSuccessful();
        $form = $crawler->selectButton('Delete')->form();
        $client->submit($form);
        $client->followRedirect();
        $this->assertSelectorTextNotContains('body', 'Delete Me');
    }

    protected function tearDown(): void
    {
        $entityManager = static::getContainer()->get('doctrine')->getManager();
        $connection = $entityManager->getConnection();
        $platform = $connection->getDatabasePlatform();
        $connection->executeStatement($platform->getTruncateTableSQL('category', true));
        parent::tearDown();
    }
}
