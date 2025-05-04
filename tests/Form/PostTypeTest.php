<?php

namespace App\Tests\Form;

use App\Entity\Category;
use App\Entity\Post;
use App\Form\PostType;
use Symfony\Component\Form\PreloadedExtension;
use Symfony\Component\Form\Test\TypeTestCase;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PostTypeTest extends TypeTestCase
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
        $formData = [
            'title' => 'Test title',
            'content' => 'Test content',
            'excerpt' => 'Short excerpt',
            'status' => 'draft',
            'category' => $this->category,
        ];

        $form = $this->factory->create(PostType::class, new Post());
        $form->submit($formData);

        $this->assertTrue($form->isSynchronized());
        $this->assertTrue($form->isValid());
        $post = $form->getData();
        $this->assertEquals('Test title', $post->getTitle());
        $this->assertEquals('Test content', $post->getContent());
        $this->assertEquals('Short excerpt', $post->getExcerpt());
        $this->assertEquals('draft', $post->getStatus());
        $this->assertSame($this->category, $post->getCategory());
    }

    public function testFieldsExist(): void
    {
        $form = $this->factory->create(PostType::class, new Post());
        $fields = ['title', 'content', 'excerpt', 'featuredImage', 'category', 'status'];
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