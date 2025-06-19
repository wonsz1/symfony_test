<?php

namespace App\Entity;

use App\Repository\PostRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Serializer\Annotation\Groups;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post as PostOperation;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\ApiProperty;
use App\Enumerations\PostStatus;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: PostRepository::class)]
#[ApiResource(
    operations: [
        new GetCollection(),
        new Get(),
        new PostOperation(),
        new Patch(),
        new Delete()
    ],
    normalizationContext: ['groups' => ['post:read']],
    denormalizationContext: ['groups' => ['post:write']]
)]
class Post
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['post:read'])]
    private int $id;

    #[ORM\Column(length: 255)]
    #[Groups(['post:read', 'post:write'])]
    #[ApiProperty(example: 'My First Post')]
    private string $title;

    #[ORM\Column(length: 255, unique: true)]
    #[Groups(['post:read', 'post:write'])]
    #[ApiProperty(example: 'my-first-post')]
    private string $slug;

    #[ORM\Column(type: 'text')]
    #[Groups(['post:read', 'post:write'])]
    #[ApiProperty(example: 'This is the content of my first post.')]
    private string $content;

    #[ORM\Column(type: 'text', nullable: true)]
    #[Groups(['post:read'])]
    private ?string $excerpt = null;

    #[ORM\Column(nullable: true)]
    private ?string $featuredImage = null;

    #[ORM\Column]
    #[Groups(['post:read'])]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(nullable: true)]
    #[Groups(['post:read'])]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['post:read'])]
    private ?\DateTimeImmutable $publishedAt = null;

    #[ORM\Column(length: 50)]
    #[Groups(['post:read', 'post:write'])]
    #[ApiProperty(example: 'draft')]
    private PostStatus $status;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'posts')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['post:read', 'post:write'])]
    #[ApiProperty(example: '/api/users/1')]
    private UserInterface $author;

    #[ORM\ManyToOne(targetEntity: Category::class, inversedBy: 'posts')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['post:read', 'post:write'])]
    #[ApiProperty(example: '/api/categories/1')]
    private Category $category;

    #[ORM\Column(type: 'integer', options: ['default' => 0])]
    #[Groups(['post:read'])]
    private int $viewCount = 0;

    #[ORM\OneToMany(mappedBy: 'post', targetEntity: Comment::class, cascade: ['remove'])]
    /** @var Collection<int, Comment> */
    private Collection $comments;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->comments = new ArrayCollection();
        $this->viewCount = 0;
    }

    public function getId(): int
    {
        return $this->id;
    }
    public function getTitle(): string
    {
        return $this->title;
    }
    public function setTitle(string $title): static
    {
        $this->title = $title;
        return $this;
    }
    public function getSlug(): string
    {
        return $this->slug;
    }
    public function setSlug(string $slug): static
    {
        $this->slug = $slug;
        return $this;
    }
    public function getContent(): string
    {
        return $this->content;
    }
    public function setContent(string $content): static
    {
        $this->content = $content;
        return $this;
    }
    public function getExcerpt(): ?string
    {
        return $this->excerpt;
    }
    public function setExcerpt(?string $excerpt): static
    {
        $this->excerpt = $excerpt;
        return $this;
    }
    public function getFeaturedImage(): ?string
    {
        return $this->featuredImage;
    }
    public function setFeaturedImage(?string $featuredImage): static
    {
        $this->featuredImage = $featuredImage;
        return $this;
    }
    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }
    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;
        return $this;
    }
    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }
    public function setUpdatedAt(?\DateTimeImmutable $updatedAt): static
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }
    public function getPublishedAt(): ?\DateTimeImmutable
    {
        return $this->publishedAt;
    }
    public function setPublishedAt(?\DateTimeImmutable $publishedAt): static
    {
        $this->publishedAt = $publishedAt;
        return $this;
    }
    public function getStatus(): string
    {
        return $this->status->value;
    }
    public function setStatus(PostStatus $status): static
    {
        $this->status = $status;
        return $this;
    }
    public function getAuthor(): UserInterface
    {
        return $this->author;
    }
    public function setAuthor(UserInterface $author): static
    {
        if (!$author instanceof User) {
            throw new \TypeError('Expected instance of ' . User::class . ', got ' . get_class($author));
        }
        $this->author = $author;
        return $this;
    }
    public function getCategory(): Category
    {
        return $this->category;
    }
    public function setCategory(Category $category): static
    {
        $this->category = $category;
        return $this;
    }
    public function getViewCount(): int
    {
        return $this->viewCount;
    }
    public function setViewCount(int $viewCount): static
    {
        $this->viewCount = $viewCount;
        return $this;
    }
    public function getComments(): Collection
    {
        return $this->comments;
    }
    public function addComment(Comment $comment): static
    {
        if (!$this->comments->contains($comment)) {
            $this->comments[] = $comment;
            $comment->setPost($this);
        } return $this;
    }
    public function removeComment(Comment $comment): static
    {
        if ($this->comments->removeElement($comment)) {
            // if ($comment->getPost() === $this) {
            //     $comment->setPost(null);
            // }
        } return $this;
    }
}
