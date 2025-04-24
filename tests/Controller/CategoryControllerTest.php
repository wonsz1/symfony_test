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
}
