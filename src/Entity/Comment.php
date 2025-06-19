<?php

namespace App\Entity;

use App\Repository\CommentRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post as PostOperation;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\ApiProperty;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: CommentRepository::class)]
#[ApiResource(
    operations: [
        new GetCollection(),
        new Get(),
        new PostOperation(),
        new Patch(),
        new Delete()
    ],
    normalizationContext: ['groups' => ['comment:read']],
    denormalizationContext: ['groups' => ['comment:write']]
)]
class Comment
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['comment:read'])]
    private int $id;

    #[ORM\Column(type: 'text')]
    #[Groups(['comment:read', 'comment:write'])]
    #[ApiProperty(example: 'This is a great post! Thanks for sharing.')]
    private string $content;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'comments')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['comment:read', 'comment:write'])]
    #[ApiProperty(example: '/api/users/1')]
    private UserInterface $author;

    #[ORM\ManyToOne(targetEntity: Post::class, inversedBy: 'comments')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['comment:read', 'comment:write'])]
    #[ApiProperty(example: '/api/posts/1')]
    private Post $post;

    #[ORM\Column]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(options: ['default' => false])]
    private bool $isApproved = false;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->isApproved = false;
    }

    public function getId(): ?int
    {
        return $this->id;
    }
    public function getContent(): ?string
    {
        return $this->content;
    }
    public function setContent(string $content): static
    {
        $this->content = $content;
        return $this;
    }
    public function getAuthor(): UserInterface
    {
        return $this->author;
    }
    public function setAuthor(UserInterface $author): static
    {
        $this->author = $author;
        return $this;
    }
    public function getPost(): Post
    {
        return $this->post;
    }
    public function setPost(Post $post): static
    {
        $this->post = $post;
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
    public function isApproved(): bool
    {
        return $this->isApproved;
    }
    public function setIsApproved(bool $isApproved): static
    {
        $this->isApproved = $isApproved;
        return $this;
    }
}
