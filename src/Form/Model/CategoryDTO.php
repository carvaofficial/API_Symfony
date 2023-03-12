<?php

namespace App\Form\Model;

use App\Entity\Category;
use Ramsey\Uuid\UuidInterface;

class CategoryDTO
{
    public ?UuidInterface $id = null;
    public ?string $name = null;

    public static function createFromBook(Category $category): self
    {
        $dto = new self();
        $dto->id = $category->getId();
        $dto->name = $category->getName();

        return $dto;
    }

    public function getId(): ?UuidInterface
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }
}
