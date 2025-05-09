<?php

namespace App\DTO;

use App\Entity\Category;

class CategoryDTO
{
    private ?string $name = null;
    private ?string $description = null;
    private ?string $slug = null;

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;
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

    public static function fromEntity(Category $category): self
    {
        $dto = new self();
        $dto->setName($category->getName());
        $dto->setDescription($category->getDescription());
        $dto->setSlug($category->getSlug());
        return $dto;
    }

    public function toEntity(?Category $category = null): Category
    {
        $category = $category ?? new Category();
        $category->setName($this->name);
        $category->setDescription($this->description);
        $category->setSlug($this->slug);
        return $category;
    }
}
