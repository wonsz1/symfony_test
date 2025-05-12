<?php

namespace App\Tests\Form;

use App\Entity\Category;
use App\DTO\PostDTO;
use App\Entity\User;
use App\Form\PostType;
use Symfony\Component\Form\PreloadedExtension;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PostTypeTest extends WebTestCase
{
    private $category;

    protected function setUp(): void
    {
        $this->category = new Category();
        $this->category->setName('Test Category');
        parent::setUp();
    }

    protected function getExtensions(): array
    {
        return [
            new PreloadedExtension([
                new EntityTypeStub([$this->category])
            ], []),
        ];
    }

    public function testSubmitValidData(): void
    {
        $client = static::createClient();

        $formData = [
            'title' => 'Test title',
            'content' => 'Test content',
            'excerpt' => 'Short excerpt',
            'status' => 'draft',
        ];

        $container = $client->getContainer();
        $form = $container->get('form.factory')->create(PostType::class, new PostDTO(), [
            'csrf_protection' => false
        ]);
        $form->submit($formData);

        $this->assertTrue($form->isSynchronized());
        $this->assertTrue($form->isValid());
        $post = $form->getData();
        $this->assertEquals('Test title', $post->getTitle());
        $this->assertEquals('Test content', $post->getContent());
        $this->assertEquals('Short excerpt', $post->getExcerpt());
        $this->assertEquals('draft', $post->getStatus());
    }

    public function testFieldsExist(): void
    {
        $form = $this->getContainer()->get('form.factory')->create(PostType::class, new PostDTO());
        $fields = ['title', 'content', 'excerpt', 'status'];
        foreach ($fields as $field) {
            $this->assertTrue($form->has($field), sprintf('Field %s is missing', $field));
        }
    }
}

// Stub for EntityType
class EntityTypeStub extends AbstractType
{
    private $choices;
    public function __construct(array $choices)
    {
        $this->choices = $choices;
    }
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'choices' => $this->choices,
            'class' => Category::class,
            'choice_label' => 'name',
        ]);
    }
    public function getBlockPrefix(): string
    {
        return 'entity';
    }
}
