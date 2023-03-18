<?php

namespace App\Form\Model;

use App\Entity\Author;
use Ramsey\Uuid\UuidInterface;

class AuthorDTO
{
    public ?UuidInterface $id = null;

    public ?string $name = null;

    public function __construct()
    {
    }

    public static function createFromAuthor(Author $author): self
    {
        $dto = new self();
        $dto->id = $author->getId();
        $dto->name = $author->getName();

        return $dto;
    }

    public function getName(): ?string
    {
        return $this->name;
    }
}
