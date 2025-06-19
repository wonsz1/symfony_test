<?php

namespace App\DTO;

use App\Entity\Post;
use App\Entity\User;
use App\Entity\Category;
use DateTimeImmutable;
use App\Enumerations\PostStatus;
use Symfony\Component\Security\Core\User\UserInterface;

class PostDTO
{
    private ?string $title = null;
    private ?string $slug = null;
    private ?string $content = null;
    private ?string $excerpt = null;
    private ?string $featuredImage = null;
    private ?DateTimeImmutable $createdAt = null;
    private ?DateTimeImmutable $updatedAt = null;
    private ?DateTimeImmutable $publishedAt = null;
    private ?PostStatus $status = null;
    private ?UserInterface $author = null;
    private ?Category $category = null;

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;
        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;
        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;
        return $this;
    }

    public function getExcerpt(): ?string
    {
        return $this->excerpt;
    }

    public function setExcerpt(?string $excerpt): self
    {
        $this->excerpt = $excerpt;
        return $this;
    }

    public function getFeaturedImage(): ?string
    {
        return $this->featuredImage;
    }

    public function setFeaturedImage(?string $featuredImage): self
    {
        $this->featuredImage = $featuredImage;
        return $this;
    }

    public function getCreatedAt(): ?DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function getUpdatedAt(): ?DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?DateTimeImmutable $updatedAt): self
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }

    public function getPublishedAt(): ?DateTimeImmutable
    {
        return $this->publishedAt;
    }

    public function setPublishedAt(?DateTimeImmutable $publishedAt): self
    {
        $this->publishedAt = $publishedAt;
        return $this;
    }

    public function getStatus(): ?PostStatus
    {
        return $this->status;
    }

    public function setStatus(PostStatus $status): self
    {
        $this->status = $status;
        return $this;
    }

    public function getAuthor(): ?UserInterface
    {
        return $this->author;
    }

    public function setAuthor(?UserInterface $author): self
    {
        $this->author = $author;
        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): self
    {
        $this->category = $category;
        return $this;
    }

    public static function fromEntity(Post $post): self
    {
        $dto = new self();
        $dto->setTitle($post->getTitle());
        $dto->setSlug($post->getSlug());
        $dto->setContent($post->getContent());
        $dto->setExcerpt($post->getExcerpt());
        $dto->setFeaturedImage($post->getFeaturedImage());
        $dto->setCreatedAt($post->getCreatedAt());
        $dto->setUpdatedAt($post->getUpdatedAt());
        $dto->setPublishedAt($post->getPublishedAt());
        $dto->setStatus(PostStatus::from($post->getStatus()));
        $dto->setAuthor($post->getAuthor());
        $dto->setCategory($post->getCategory());
        return $dto;
    }

    public function toEntity(?Post $post = null): Post
    {
        $post = $post ?? new Post();
        $post->setTitle($this->title);
        $post->setSlug($this->slug);
        $post->setContent($this->content);
        $post->setExcerpt($this->excerpt);
        $post->setFeaturedImage($this->featuredImage);
        if ($this->createdAt) {
            $post->setCreatedAt($this->createdAt);
        }
        $post->setUpdatedAt($this->updatedAt);
        $post->setPublishedAt($this->publishedAt);
        $post->setStatus($this->status);
        $post->setAuthor($this->author);
        $post->setCategory($this->category);
        return $post;
    }
}
